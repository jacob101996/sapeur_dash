<?php

namespace App\Form\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\File\File;

class FileToStringTransformer implements DataTransformerInterface
{
    private $em;
    private $container;

    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container)
    {
        $this->em        = $entityManager;
        $this->container = $container;
    }

    /**
     * @param mixed $value
     * @return null|File
     */
    public function transform($value)
    {
        try {
            if (null === $value) {
                return null;
            }

            if(strlen($value) == 0) {
                return null;
            }

            return new File($_ENV['DIR_IMG_PRODUCT'] . '/'. $value);
        }
        catch (TransformationFailedException $e) {
            return null;
        }
    }
    /**
     * @param mixed $value
     * @return string
     */
    public function reverseTransform($value)
    {
        try {
            if(is_null($value)) {
                return '';
            }
            else {
                return $value;
            }
        }
        catch (TransformationFailedException $e) {
            return null;
        }
    }
}