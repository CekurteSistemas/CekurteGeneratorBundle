<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cekurte\GeneratorBundle\Generator;

use Sensio\Bundle\GeneratorBundle\Generator\DoctrineFormGenerator as Generator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Generates a form class based on a Doctrine entity.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Hugo Hamon <hugo.hamon@sensio.com>
 */
class DoctrineFormGenerator extends Generator
{
    private $filesystem;
    private $className;
    private $classPath;

    /**
     * Generates the entity form class if it does not exist.
     *
     * @param BundleInterface   $bundle   The bundle in which to create the class
     * @param string            $entity   The entity relative class name
     * @param ClassMetadataInfo $metadata The entity metadata class
     */
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata)
    {
        $parts       = explode('\\', $entity);
        $entityClass = array_pop($parts);

        $dirPath     = $bundle->getPath() . '/Form';

        $configs     = array(
            'type'          => array(
                'path'      => 'Type',
                'suffix'    => 'FormType',
            ),
            'handler'       => array(
                'path'      => 'Handler',
                'suffix'    => 'FormHandler',
            ),
        );

        foreach ($configs as $classConfig) {

            $this->className = $entityClass . $classConfig['suffix'];

            $this->classPath = $dirPath . '/' . $classConfig['path'] . '/' . $this->className . '.php';

            if (file_exists($this->classPath)) {
                throw new \RuntimeException(sprintf('Unable to generate the %s form class as it already exists under the %s file', $this->className, $this->classPath));
            }

            if (count($metadata->identifier) > 1) {
                throw new \RuntimeException('The form generator does not support entity classes with multiple primary keys.');
            }

            $parts = explode('\\', $entity);
            array_pop($parts);

            $this->renderFile('form/' . $classConfig['suffix'] . '.php.twig', $this->classPath, array(
                'fields'           => $this->getFieldMappingsFromMetadata($metadata),
                'namespace'        => $bundle->getNamespace(),
                'entity_namespace' => implode('\\', $parts),
                'entity_class'     => $entityClass,
                'bundle'           => $bundle->getName(),
                'form_class'       => $this->className,
                'form_type_name'   => strtolower(str_replace('\\', '_', $bundle->getNamespace()).($parts ? '_' : '').implode('_', $parts).'_'.substr($this->className, 0, -4)),
            ));
        }
    }

    private function getFieldMappingsFromMetadata(ClassMetadataInfo $metadata)
    {
        $fieldMappings = (array) $metadata->fieldMappings;

        // Remove the primary key field if it's not managed manually
        if (!$metadata->isIdentifierNatural()) {
            foreach ($fieldMappings as $fieldName => $fieldMapping) {
                if (in_array($fieldName, $metadata->identifier)) {
                    unset($fieldMappings[$fieldName]);
                }
            }
        }

        return $fieldMappings;
    }
}
