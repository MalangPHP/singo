<?php


namespace Singo;

use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Monolog\Logger;
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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
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
        $this->initConfig();
        $this->initLogger();
        $this->initDatabase();
        $this->initValidator();
        $this->initMailer();
        $this->initCommandBus();
        $this->initFractal();
        $this->initDefaultSubscribers();
        $this->initControllerService();

        /**
         * Silex config
         */
        $this["debug"] = $this["sable.config"]->get("common/debug");

        /**
         * Save container in static variable
         */
        self::$container = $this;
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
        $mapping = $this["sable.config"]->get("database/orm/mappings");
        $mapping["path"] = APP_PATH . $mapping["path"];

        $this->register(
            new DoctrineServiceProvider(),
            [
                "db.options" => $this["sable.config"]->get("database/connection")
            ]
        );

        $this->register(
            new DoctrineOrmServiceProvider(),
            [
                "orm.proxies_dir" => dirname(__FILE__) . $this["sable.config"]->get("database/orm/proxy_dir"),
                "orm.proxies_namespace" => $this["sable.config"]->get("database/orm/proxy_namespace"),
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
        $log_file = APP_PATH . $this["sable.config"]->get("common/log/dir") . "/{$date->format("Y-m-d")}.log";
        $this->register(
            new MonologServiceProvider(),
            [
                "monolog.logfile" => $log_file,
                "monolog.name" => $this["sable.config"]->get("common/log/name"),
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
        $this["swiftmailer.options"] = $this["sable.config"]->get("mailer");
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
        $this["dispatcher"]->addSubscriber(function () {
            return new ExceptionHandler($this);
        });
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
     * @param EventSubscriberInterface $subscriber
     * @TODO Implement container aware event dispatcher
     */
    public function registerSubscriber(EventSubscriberInterface $subscriber)
    {
        $this["dispatcher"]->addSubscriber($subscriber);
    }
}

// EOF
