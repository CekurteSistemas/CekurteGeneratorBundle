<?php

namespace Cekurte\GeneratorBundle\Controller;

use JMS\Serializer\Serializer;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Rest Controller Interface
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 1.0
 */
interface RestControllerInterface
{
    /**
     * Get a instance of Serializer
     *
     * @return Serializer
     */
    public function getSerializer();


    /**
     * Get Accept header response from Request.
     *
     * @param Request $request
     * @return string
     */
    public function getAcceptResponse(Request $request);

    /**
     * Create a response by data format.
     *
     * @param mixed $data
     * @param string $format
     *
     * @return array|JsonResponse
     */
    public function createResponse($data, $format);

    /**
     * Create a response by data format when resource has errors because the form is invalid.
     *
     * @param FormInterface $form
     * @param string $format
     * @param array $optionalData
     * @param string $formName
     *
     * @return array|JsonResponse
     */
    public function createResponseWhenFormIsInvalid(FormInterface $form, $format, $optionalData = array(), $formName = 'form');

    /**
     * Create a response by data format when resource was created.
     *
     * @param mixed $identifier
     * @param string $format
     * @param string|null $message
     *
     * @return JsonResponse|mixed
     */
    public function createResponseWhenResourceWasCreated($identifier, $format, $message = null);

    /**
     * Create a response by data format when resource cannot be created.
     *
     * @param \Exception $exception
     * @param string $format
     * @param string|null $message
     *
     * @return JsonResponse|null
     */
    public function createResponseWhenResourceCannotBeCreated(\Exception $exception, $format, $message = null);

    /**
     * Create a response by data format when resource was updated.
     *
     * @param mixed $identifier
     * @param string $format
     * @param string|null $message
     *
     * @return JsonResponse|mixed
     */
    public function createResponseWhenResourceWasUpdated($identifier, $format, $message = null);

    /**
     * Create a response by data format when resource cannot be updated.
     *
     * @param \Exception $exception
     * @param string $format
     * @param string|null $message
     *
     * @return JsonResponse|null
     */
    public function createResponseWhenResourceCannotBeUpdated(\Exception $exception, $format, $message = null);

    /**
     * Create a response by data format when resource was deleted.
     *
     * @param mixed $identifier
     * @param string $format
     * @param string|null $message
     *
     * @return JsonResponse|mixed
     */
    public function createResponseWhenResourceWasDeleted($identifier, $format, $message = null);

    /**
     * Create a response by data format when resource cannot be deleted.
     *
     * @param \Exception $exception
     * @param string $format
     * @param string|null $message
     *
     * @return JsonResponse|null
     */
    public function createResponseWhenResourceCannotBeDeleted(\Exception $exception, $format, $message = null);

    /**
     * Create a response by data format when resource cannot be retrived.
     *
     * @param \Exception $exception
     * @param string $format
     * @param string|null $message
     *
     * @return JsonResponse|null
     */
    public function createResponseWhenResourceCannotBeRetrived(\Exception $exception, $format, $message = null);
}
