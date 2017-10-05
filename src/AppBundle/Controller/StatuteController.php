<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Statute;
use AppBundle\Form\StatuteType;

/**
 * Statute controller.
 *
 * @Route("/statute")
 */
class StatuteController extends Controller
{

    /**
     * Lists all Statute entities.
     *
     * @Route("/", name="statute")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Statute')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Statute entity.
     *
     * @Route("/", name="statute_create")
     * @Method("POST")
     * @Template("AppBundle:Statute:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Statute();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('statute_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Statute entity.
     *
     * @param Statute $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Statute $entity)
    {
        $form = $this->createForm(new StatuteType(), $entity, array(
            'action' => $this->generateUrl('statute_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Statute entity.
     *
     * @Route("/new", name="statute_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Statute();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Statute entity.
     *
     * @Route("/{id}", name="statute_show")
     * @Route("/{statuteCode}", name="fda_statute_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Statute')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Statute entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Statute entity.
     *
     * @Route("/{id}/edit", name="statute_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Statute')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Statute entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Statute entity.
    *
    * @param Statute $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Statute $entity)
    {
        $form = $this->createForm(new StatuteType(), $entity, array(
            'action' => $this->generateUrl('statute_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Statute entity.
     *
     * @Route("/{id}", name="statute_update")
     * @Method("PUT")
     * @Template("AppBundle:Statute:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Statute')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Statute entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('statute_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Statute entity.
     *
     * @Route("/{id}", name="statute_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Statute')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Statute entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('statute'));
    }

    /**
     * Creates a form to delete a Statute entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('statute_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
