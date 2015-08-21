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

        function getId()
        {
            return $this->id;
        }

        function getStylistId()
        {
            return $this->stylist_id;
        }

        function setStylistId($new_stylist_id)
        {
            $this->stylist_id = $new_stylist_id;
        }

        function getName()
        {
            return $this->name;
        }


        function setName($new_name)
        {
            $this->name = $new_name;
        }

        function save()
        {
            $GLOBALS['DB']->exec("INSERT INTO clients (stylist_id, name) VALUES ({$this->getstylistId()}, '{$this->getName()}');");
            $this->id = $GLOBALS['DB']->lastInsertId();
        }

        function delete()
        {
            $GLOBALS['DB']->exec("DELETE FROM clients WHERE id = {$this->getId()};");
        }

        static function findByClientId($search_id)
        {
            $found_client = null;
            $clients = Client::getAll();
            foreach ($clients as $client){
                $client_id = $client->getId();
                if ($client_id == $search_id){
                    $found_client = $client;
                }
            }
            return $found_client;
        }

        static function findByStylistId($search_id)
        {
            $found_clients = array();
            $clients = client::getAll();
            foreach ($clients as $client){
                $stylist_id = $client->getStylistId();
                if ($stylist_id == $search_id){
                    array_push($found_clients, $client);
                }
            }
            return $found_clients;
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

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM clients;");
        }
    }
 ?>
