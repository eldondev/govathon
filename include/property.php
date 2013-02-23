<?php

class Table {

}

class Property {

  static function db() {
    return new PDO ('mysql:host=localhost;dbname=teamvacant', 'root', 'hello');
  }

  /*
  static function new_record($params) {
    return new Property($params);
  }*/

  static function find($id) {
    $db = Property::db();
    $stmt = $db->prepare("SELECT * FROM properties WHERE id = ?");
    $stmt->execute(array($_GET['id']));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $row = $rows[0];
    return new Property($row);
  }

  /*
  public function attrs() {
    return array('address'
  }
  */

  public function __construct($params) {
    $this->data = $params;
  }

  public function get($key) {
    return isset($this->data[$key]) ? $this->data[$key] : null;
  }

  public function set($key, $val) {
    $this->data[$key] = $val;
  }

  function id() {
    return $this->data['id'];
  }

  function save() {
    $db = Property::db();
    $values = array();
    if ($this->data['id']) {
      $query = 'UPDATE properties SET ';
      $sets = array();
      foreach ($this->data as $key => $val) {
        if ($key != 'id') {
          $sets[] = "$key = ?";
          $values[] = $val;
        }
      }
      $query .= ' ' . join(', ', $sets);

      $query .= ' WHERE id = ?';
      $values[] = $this->data['id'];

      print $query;
      $stmt = $db->prepare($query);
      $stmt->execute($values);
    } else {
      $query = 'INSERT into properties ';
      $keys = array();
      $qs = array();
      foreach ($this->data as $key => $val) {
        if ($key != 'id') {
          $keys[] = $key;
          $qs[] = '?';
          $values[] = $val;
        }
      }
      $query .= ' (' . join(', ', $keys) . ') VALUES (' . join(', ', $qs) . ')';

      $stmt = $db->prepare($query);
      $stmt->execute($values);
      $this->data['id'] = $db->lastInsertId();
    }
  }

  function relations($type = null) {

  }

  function addRelation($type, $params) {

  }

  function deleteRelation($id) {

  }

}

?>
