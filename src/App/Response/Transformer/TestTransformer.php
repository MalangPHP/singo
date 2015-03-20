<?php


namespace Singo\App\Response\Transformer;

use League\Fractal\TransformerAbstract;

/**
 * Class TestTransformer
 * @package Singo\Response\Transformer
 */
class TestTransformer extends TransformerAbstract
{
    /**
     * @param array $array
     * @return array
     */
    public function transform(array $array)
    {
        return [
            "name"  => $array["name"],
            "email" => $array["email"],
            "location" => $array["location"]
        ];
    }
}

// EOF
