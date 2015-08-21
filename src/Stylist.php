<?php
    class Stylist
    {
        private $id;
        private $name;

        function __construct($name, $id = null)
        {
            $this->id = $id;
            $this->name = $name;
        }


        function getName()
        {
            return $this->name;
        }

        function setName($new_name)
        {
            $this->name = $new_name;
        }

        function getId()
        {
            return $this->id;
        }

        function save()
        {
            $GLOBALS['DB']->exec("INSERT INTO stylists (name) VALUES ('{$this->getname()}');");
            $this->id = $GLOBALS['DB']->lastInsertId();
        }

        function update($new_name)
        {
            $GLOBALS['DB']->exec("UPDATE stylists SET name = '{$new_name}' WHERE id = {$this->getId()};");
            $this->setname($new_name);
        }

        static function find($search_id)
        {
            $found_stylist = null;
            $stylists_to_search = Stylist::getAll();
            foreach($stylists_to_search as $stylist){
                $stylist_id = $stylist->getId();
                if($stylist_id === $search_id){
                    $found_stylist = $stylist;
                }
            }
            return $found_stylist;
        }

        static function getAll()
        {
            $returned_stylists = $GLOBALS['DB']->query("SELECT * FROM stylists;");
            $stylists = array();
            foreach($returned_stylists as $stylist){
                $name = $stylist['name'];
                $id = $stylist['id'];
                $new_stylist = new Stylist($name, $id);
                array_push($stylists, $new_stylist);
            }
            return $stylists;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM stylists;");
        }


    }
?>
