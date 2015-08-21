<?php
    class Client
    {
        private $name;
        private $stylist_id;
        private $id;

        function __construct($name, $stylist_id, $id = null)
        {
            $this->name = $name;
            $this->stylist_id = $stylist_id;
            $this->id = $id;
        }

        function getStylistId()
        {
            return $this->stylist_id;
        }

        function getName()
        {
            return $this->name;
        }

        function getId()
        {
            return $this->id;
        }

        function setName($new_name)
        {
            $this->name = $new_name;
        }

        function setStylistId($new_stylist_id)
        {
            $this->stylist_id = $new_stylist_id;
        }

        function save()
        {
            $GLOBALS['DB']->exec("INSERT INTO clients (stylist_id, name) VALUES ({$this->getstylistId()}, '{$this->getName()}');");
            $this->id = $GLOBALS['DB']->lastInsertId();
        }

        static function getAll()
        {
            $returned_clients = $GLOBALS['DB']->query("SELECT * FROM clients;");
            $clients = array();
            foreach($returned_clients as $client){
                $stylist_id = $client['stylist_id'];
                $name = $client['name'];
                $id = $client['id'];
                $new_client = new Client($name, $stylist_id, $id);
                array_push($clients, $new_client);
            }
            return $clients;
        }
    }
 ?>
