<?php


namespace Abstraction;

use Restfull\Container\Instances;
use GoogleMap\Map;
use GoogleMap\MapComponent;
use GoogleMap\MapHelper;

class Gmaps
{

    /**
     * @var Map
     */
    private $map;

    /**
     * @var Instances
     */
    private $instance;

    /**
     * Gmaps constructor.
     * @param int $count
     */
    public function __construct()
    {
        $this->instance = new Instances();
        $this->map = $this->instance->resolveClass(
                'GoogleMap' . DS_REVERSE . 'Map',
                ['key' => key_api]
        );
        return $this;
    }

    public function coordernates(array $datas): array
    {
        $keys = ['addressaddresses', 'number', 'cityaddresses', 'districtaddresses', 'stateaddresses'];
        foreach ($keys as $key) {
            if (stripos($key, 'addresses') !== false) {
                $address[substr($key, 0, stripos($key, 'addresses'))] = $datas[$key];
                unset($datas[$key]);
            } else {
                $address[$key] = $datas[$key];
            }
        }
        $this->map->setGeolocation($address);
        $component = $this->instance->resolveClass('GoogleMap' . DS_REVERSE . 'MapComponent');
        $address['zipcode'] = $datas['zipcodeaddresses'];
        unset($datas['zipcodeaddresses']);
        list($lat, $long) = $component->coordenation($this->map);
        return [array_merge($datas, ['latitude' => $lat, 'longitude' => $long]), $address];
    }

    public function render(object $helper, string $path, array $options = []): string
    {
        $this->map->setGeolocationCenterMap(-14.235004, -51.92528);
        if (count($options) > 0) {
            $width = 0;
            foreach ($options as $key => $value) {
                if ($key == 'width') {
                    $width = $value;
                } elseif ($key == 'height') {
                    $this->map->setWidthAndHeight($width, $value);
                } elseif ($key == 'zoom') {
                    $this->map->setZoom($value);
                } else {
                    $this->map->setOptions($key, $value);
                }
            }
        }
        $helper = $this->instance->resolveClass(
                'GoogleMap' . DS_REVERSE . 'MapHelper',
                ['helper' => $helper, 'map' => $this->map]
        );
        return $helper->render($path);
    }
}