<?php


namespace Singo;

use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Monolog\Logger;
use Silex\Provider\SecurityJWTServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Singo\Event\Listener\ExceptionHandler;
use Singo\Providers\CommandBusServiceProvider;
use Singo\Providers\ConfigServiceProvider;
use Singo\Providers\FractalServiceProvider;
use Silex\Application as SilexApplication;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Singo\Providers\PimpleAwareEventDispatcherServiceProvider;
use Singo\Providers\UserServiceProvider;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;

/**
 * Class Application
 * @package Singo
 */
class Application extends SilexApplication
{
    /**
     * @var Application
     */
    public static $container;

    /**
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        define("APP_PATH", dirname(__FILE__));
        define("PUBLIC_PATH", dirname(__FILE__) . "/../public");
        parent::__construct($values);
    }

    /**
     * initialize our application
     */
    public function init()
    {
        $this->initPimpleAwareEventDispatcher();
        $this->initConfig();
        $this->initLogger();
        $this->initDatabase();
        $this->initValidator();
        $this->initMailer();
        $this->initFirewall();
        $this->initCommandBus();
        $this->initFractal();
        $this->initDefaultSubscribers();
        $this->initControllerService();

        /**
         * Silex config
         */
        $this["debug"] = $this["singo.config"]->get("common/debug");

        /**
         * Save container in static variable
         */
        self::$container = $this;
    }

    /**
     * return void
     */
    public function initPimpleAwareEventDispatcher()
    {
        $this->register(new PimpleAwareEventDispatcherServiceProvider());
    }

    /**
     * initialize application configuration
     * return void
     */
    public function initConfig()
    {
        $this->register(new ConfigServiceProvider());
    }

    /**
     * initialize doctrine orm and dbal
     * return void
     */
    public function initDatabase()
    {
        $mapping = $this["singo.config"]->get("database/orm/mappings");
        $mapping["path"] = APP_PATH . $mapping["path"];

        $this->register(
            new DoctrineServiceProvider(),
            [
                "db.options" => $this["singo.config"]->get("database/connection")
            ]
        );

        $this->register(
            new DoctrineOrmServiceProvider(),
            [
                "orm.proxies_dir" => dirname(__FILE__) . $this["singo.config"]->get("database/orm/proxy_dir"),
                "orm.proxies_namespace" => $this["singo.config"]->get("database/orm/proxy_namespace"),
                "orm.em.options" => [
                    "mappings" => [
                        $mapping
                    ]
                ]
            ]
        );
    }

    /**
     * initialize logger
     * return void
     */
    public function initLogger()
    {
        $date = new \DateTime();
        $log_file = APP_PATH . $this["singo.config"]->get("common/log/dir") . "/{$date->format("Y-m-d")}.log";
        $this->register(
            new MonologServiceProvider(),
            [
                "monolog.logfile" => $log_file,
                "monolog.name" => $this["singo.config"]->get("common/log/name"),
                "monolog.level" => Logger::INFO
            ]
        );
    }

    /**
     * initialize validator
     * return void
     */
    public function initValidator()
    {
        $this->register(new ValidatorServiceProvider());
        $this["validator.mapping.class_metadata_factory"] = function () {
            foreach (spl_autoload_functions() as $fn) {
                AnnotationRegistry::registerLoader($fn);
            }

            $reader = new AnnotationReader();
            $loader = new AnnotationLoader($reader);
            return new LazyLoadingMetadataFactory($loader);
        };
    }

    /**
     * initialize mailer
     * return void
     */
    public function initMailer()
    {
        $this["swiftmailer.options"] = $this["singo.config"]->get("mailer");
        $this->register(new SwiftmailerServiceProvider());
    }

    /**
     * initialize command bus
     * return void
     */
    public function initCommandBus()
    {
        $this->register(new CommandBusServiceProvider());
    }

    /**
     * initialize controller as a service
     * return void
     */
    public function initControllerService()
    {
        $this->register(new ServiceControllerServiceProvider());
    }

    /**
     * initialize array processor to json
     * return void
     */
    public function initFractal()
    {
        $this->register(new FractalServiceProvider());
    }

    /**
     * initialize default event subscriber
     * return void
     */
    public function initDefaultSubscribers()
    {
        $this->registerSubscriber(
            ExceptionHandler::class,
            function () {
                return new ExceptionHandler($this);
            }
        );
    }

    /**
     * initialize web application firewall
     * return void
     */
    public function initFirewall()
    {
        $this["users"] = function () {
            return new UserServiceProvider();
        };
        $this["security.jwt"] = $this["singo.config"]->get("jwt");
        $this->register(new SecurityJWTServiceProvider());
        $this->register(new SecurityServiceProvider());
        $this["security.firewalls"] = $this["singo.config"]->get("firewall");
    }

    /**
     * register command for our application
     * @param array $commands
     * @param callable $handler
     */
    public function registerCommands(array $commands, callable $handler)
    {
        foreach ($commands as $command) {
            $handler_id = "app.handler." . join('', array_slice(explode("\\", $command), -1));
            $this[$handler_id] = $handler;
        }
    }

    /**
     * @param string $class
     * @param callable $callback
     */
    public function registerSubscriber($class, callable $callback)
    {
        $service_id = "event." . strtolower(str_replace("\\", ".", $class));

        $this[$service_id] = $callback;

        $this["dispatcher"]->addSubscriberService($service_id, $class);
    }
}

// EOF
