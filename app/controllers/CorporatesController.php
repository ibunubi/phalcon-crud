<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;


class CorporatesController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    /**
     * Searches for corporates
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Corporates', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "id";

        $corporates = Corporates::find($parameters);
        if (count($corporates) == 0) {
            $this->flash->notice("The search did not find any corporates");

            $this->dispatcher->forward([
                "controller" => "corporates",
                "action" => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $corporates,
            'limit'=> 10,
            'page' => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {

    }

    /**
     * Edits a corporate
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {

            $corporate = Corporates::findFirstByid($id);
            if (!$corporate) {
                $this->flash->error("corporate was not found");

                $this->dispatcher->forward([
                    'controller' => "corporates",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->id = $corporate->id;

            $this->tag->setDefault("id", $corporate->id);
            $this->tag->setDefault("name", $corporate->name);
            $this->tag->setDefault("site", $corporate->site);
            
        }
    }

    /**
     * Creates a new corporate
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "corporates",
                'action' => 'index'
            ]);

            return;
        }

        $corporate = new Corporates();
        $corporate->name = $this->request->getPost("name");
        $corporate->site = $this->request->getPost("site");
        

        if (!$corporate->save()) {
            foreach ($corporate->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "corporates",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("corporate was created successfully");

        $this->dispatcher->forward([
            'controller' => "corporates",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a corporate edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "corporates",
                'action' => 'index'
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $corporate = Corporates::findFirstByid($id);

        if (!$corporate) {
            $this->flash->error("corporate does not exist " . $id);

            $this->dispatcher->forward([
                'controller' => "corporates",
                'action' => 'index'
            ]);

            return;
        }

        $corporate->name = $this->request->getPost("name");
        $corporate->site = $this->request->getPost("site");
        

        if (!$corporate->save()) {

            foreach ($corporate->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "corporates",
                'action' => 'edit',
                'params' => [$corporate->id]
            ]);

            return;
        }

        $this->flash->success("corporate was updated successfully");

        $this->dispatcher->forward([
            'controller' => "corporates",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a corporate
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        $corporate = Corporates::findFirstByid($id);
        if (!$corporate) {
            $this->flash->error("corporate was not found");

            $this->dispatcher->forward([
                'controller' => "corporates",
                'action' => 'index'
            ]);

            return;
        }

        if (!$corporate->delete()) {

            foreach ($corporate->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "corporates",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("corporate was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "corporates",
            'action' => "index"
        ]);
    }

}
