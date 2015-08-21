<?php
    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */
    require_once "src/Stylist.php";
    require_once "src/Client.php";
    // require_once "src/client.php";
    $server = 'mysql:host=localhost;dbname=hair_salon_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class ClientTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
        {
            Client::deleteAll();
        }

        function test_Save()
        {
            //Arrange
            $stylist_id = 1;
            $name = "Chris";
            $test_client = new Client($name, $stylist_id);
            //Act
            $test_client->save();
            //var_dump($test_client);
            $result = Client::getAll();
            //Assert
            $this->assertEquals($test_client, $result[0]);
        }

        function test_getAll()
        {
            //Arrange
            $name1 = "Joe Schmoe";
            $name2 = "Frizzy Lizzy";
            $stylist_id = 1;
            $test_client1 = new Client($name1, $stylist_id);
            $test_client1->save();
            $test_client2 = new Client($name1, $stylist_id);
            $test_client2->save();
            //Act
            $result = Client::getAll();
            //Assert
            $this->assertEquals([$test_client1, $test_client2], $result);
        }

        function test_deleteAll()
        {
            //Arrange
            $stylist_id = 1;
            $name1 = "Joe";
            $name2 = "Liz";
            $test_client1 = new Client($name1, $stylist_id);
            $test_client1->save();
            $test_client2 = new Client($name1, $stylist_id);
            $test_client2->save();
            //Act
            Client::deleteAll();
            $result = Client::getAll();
            //Assert
            $this->assertEquals([], $result);
        }
        function test_getId()
        {
            //Arrange
            $name = "Bob";
            $stylist_id = 1;
            $test_client = new client($name, $stylist_id);
            $test_client->save();
            //Act
            $result = $test_client->getId();
            //Assert
            $this->assertEquals(true, is_numeric($result));
        }

        function test_findByClientId()
        {
            //Arrange
            $stylist_id1 = 1;
            $name1 = "Joe Bob";
            $test_client1 = new Client($name1, $stylist_id1);
            $test_client1->save();
            $name2 = "Bobbi Jo";
            $stylist_id2 = 2;
            $test_client2 = new Client($name2, $stylist_id2);
            $test_client2->save();
            //Act
            $result = Client::findByClientId($test_client1->getId());
            //Assert
            $this->assertEquals($test_client1, $result);
        }

        function test_findByStylistId()
        {
            //Arrange
            $stylist_id1 = 1;
            $name1 = "Joe Bob";
            $test_client1 = new Client($name1, $stylist_id1);
            $test_client1->save();
            $name2 = "Bobbi Jo";
            $stylist_id2 = 2;
            $test_client2 = new Client($name2, $stylist_id2);
            $test_client2->save();
            //Act
            $result = Client::findByStylistId(1);
            //Assert
            $this->assertEquals([$test_client1], $result);
        }

        function test_setName()
        {
            //Arrange
            $name = "Joe";
            $test_client = new client(1, $name);
            $test_client->save();
            $new_name = "Joe Schmoe";
            //Act
            $test_client->setName($new_name);
            $result = $test_client->getName();
            //Assert
            $this->assertEquals($new_name, $result);
        }

        function test_setStylistId()
        {
            //Arrange
            $name = "Joe";
            $stylist_id = 1;
            $test_client = new Client($stylist_id, $name);
            $test_client->save();
            $new_stylist_id = 2;
            //Act
            $test_client->setStylistId($new_stylist_id);
            $result = $test_client->getStylistId();
            //Assert
            $this->assertEquals($new_stylist_id, $result);
        }

        function test_delete()
        {
            //Arrange
            $name1 = "Joe";
            $test_client1 = new client($name1, 1);
            $test_client1->save();
            $name2 = "Bob";
            $test_client2 = new client($name2, 1);
            $test_client2->save();
            //Act
            $test_client1->delete();
            $result = Client::getAll();
            //Assert
            $this->assertEquals([$test_client2], $result);
        }
    }
?>
