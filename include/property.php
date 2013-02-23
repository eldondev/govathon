<?php

class Table {
  static function db() {
    return new PDO ('mysql:host=localhost;dbname=teamvacant', 'root', 'hello');
  }

  static function find($table, $id) {
    $db = Table::db();
    $stmt = $db->prepare("SELECT * FROM " . $table . " WHERE id = ?");
    $stmt->execute(array($_GET['id']));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $row = $rows[0];
    return new Property($table, $row);
  }

  public function __construct($table, $params) {
    $this->table = $table;
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
    $db = Table::db();
    $class = get_class();
    $values = array();
    if (isset($this->data['id']) && $this->data['id']) {
      $query = 'UPDATE ' . $this->table . ' SET ';
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

      $stmt = $db->prepare($query);
      $stmt->execute($values);
    } else {
      $query = 'INSERT into ' . $this->table . ' ';
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
}

class Property extends Table {

  static function tableName() {
    return 'properties';
  }

  function browserAddress() {
    $db = Table::db();
    $stmt = $db->prepare("SELECT * FROM resources WHERE meta='browser_address' and property_id = ?");

    $stmt->execute(array($this->id()));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($rows)) {
      $row = $rows[0];
      return new Table('resources', $row);
    } else {
      return null;
    }
  }

  function photos() {
    $photos = array();

    $db = Table::db();
    $stmt = $db->prepare("SELECT * FROM resources WHERE meta='photo' and property_id = ?");

    $stmt->execute(array($this->id()));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row) {
      $photos[] = new Table('resources', $row);
    }

    return $photos;
  }

  function relations($type = null) {

  }

  function addRelation($type, $params) {

  }

  function deleteRelation($id) {

  }

}

?>
