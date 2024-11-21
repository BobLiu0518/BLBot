<?php

namespace BLBot;

class Database {
    private $collection;
    private $primaryKey;
    private $defaultValue;

    public function __construct($collectionName, $options = []) {
        global $Database;
        $this->collection = $Database->$collectionName;
        $this->primaryKey = $options['key'] ?? 'user_id';
        $this->defaultValue = $options['default'] ?? null;
    }

    public function set($key, $data, $upsert = true) {
        return $this->collection->updateOne(
            [$this->primaryKey => $key],
            ['$set' => $data],
            ['upsert' => $upsert],
        )->isAcknowledged();
    }

    public function get($key) {
        return $this->collection->findOne(
            [$this->primaryKey => $key],
        ) ?? $this->defaultValue;
    }

    public function delete($key) {
        return $this->collection->deleteOne(
            [$this->primaryKey => $key],
        )->getDeletedCount();
    }
}
