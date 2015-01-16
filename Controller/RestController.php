<?php

namespace Cekurte\GeneratorBundle\Controller;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Rest Controller.
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 2.0
 */
abstract class RestController extends CekurteController implements RestControllerInterface
{
    /**
     * @inheritdoc
     */
    public function getSerializer()
    {
        return SerializerBuilder::create()->build();
    }

    /**
     * @inheritdoc
     */
    public function getAcceptResponse(Request $request)
    {
        $currentAcceptResponse = $request->getFormat(current($request->getAcceptableContentTypes()));

        return in_array($currentAcceptResponse, array('html', 'json')) ? $currentAcceptResponse : 'html';
    }

    /**
     * @inheritdoc
     */
    public function createResponse($data, $format)
    {
        if ($format === 'json') {

            $response = new JsonResponse();

            return $response->setContent(
                $this->getSerializer()->serialize($data, $format)
            );
        }

        return array('data' => $data);
    }

    /**
     * @inheritdoc
     */
    public function createResponseWhenFormIsInvalid(FormInterface $form, $format, $optionalData = array(), $formName = 'form')
    {
        $errors = $form->getErrors(true);

        if ($format === 'json') {
            return new JsonResponse(array(
                'message' => implode('. ', $errors)
            ), 400);
        }

        foreach ($errors as $error) {
            $this->get('session')->getFlashBag()->add('message', array(
                'type'    => 'error',
                'message' => $error->getMessage(),
            ));
        }

        return array_merge(array($formName => $form->createView()), $optionalData);
    }

    /**
     * @inheritdoc
     */
    public function createResponseWhenResourceWasCreated($identifier, $format, $message = null)
    {
        if (is_null($message)) {
            $message = 'The resource has been created with successfully';
        }

        if ($format === 'json') {
            return new JsonResponse(array(
                'message' => $message,
                'data'    => $identifier,
            ), 201);
        }

        $this->get('session')->getFlashBag()->add('message', array(
            'type'    => 'success',
            'message' => $message,
        ));

        return array(
            'data' => $identifier
        );
    }

    /**
     * @inheritdoc
     */
    public function createResponseWhenResourceCannotBeCreated(\Exception $exception, $format, $message = null)
    {
        if (is_null($message)) {
            $message = sprintf('The resource cannot be created');
        }

        if ($format === 'json') {
            return new JsonResponse(array(
                'message'     => $message,
                'exception'   => array(
                    'class'   => get_class($exception),
                    'message' => $exception->getMessage(),
                ),
            ), 500);
        }

        $this->get('session')->getFlashBag()->add('message', array(
            'type'        => 'error',
            'message'     => $message,
            'exception'   => array(
                'class'   => get_class($exception),
                'message' => $exception->getMessage(),
            ),
        ));

        return null;
    }

    /**
     * @inheritdoc
     */
    public function createResponseWhenResourceWasUpdated($identifier, $format, $message = null)
    {
        if (is_null($message)) {
            $message = 'The resource has been updated with successfully';
        }

        return $this->createResponseWhenResourceWasCreated($identifier, $format, $message);
    }

    /**
     * @inheritdoc
     */
    public function createResponseWhenResourceCannotBeUpdated(\Exception $exception, $format, $message = null)
    {
        if (is_null($message)) {
            $message = sprintf('The resource cannot be updated');
        }

        return $this->createResponseWhenResourceCannotBeCreated($exception, $format, $message);
    }

    /**
     * @inheritdoc
     */
    public function createResponseWhenResourceWasDeleted($identifier, $format, $message = null)
    {
        if (is_null($message)) {
            $message = 'The resource has been deleted with successfully';
        }

        return $this->createResponseWhenResourceWasCreated($identifier, $format, $message);
    }

    /**
     * @inheritdoc
     */
    public function createResponseWhenResourceCannotBeDeleted(\Exception $exception, $format, $message = null)
    {
        if (is_null($message)) {
            $message = sprintf('The resource cannot be deleted');
        }

        return $this->createResponseWhenResourceCannotBeCreated($exception, $format, $message);
    }

    /**
     * @inheritdoc
     */
    public function createResponseWhenResourceCannotBeRetrived(\Exception $exception, $format, $message = null)
    {
        if (is_null($message)) {
            $message = sprintf('The resource cannot be retrived');
        }

        return $this->createResponseWhenResourceCannotBeCreated($exception, $format, $message);
    }
}
