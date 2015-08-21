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
            var_dump($test_client1);
            $test_client2 = new Client($name1, $stylist_id);
            $test_client2->save();
            //Act
            $result = Client::getAll();
            var_dump($result);
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
    }
?>
