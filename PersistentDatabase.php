<?php

/**
 *
 * @author dibriel
 */
interface PersistentDatabase {

    //must return last id
    public function write($table, $data);
    public function find($table, $proyection, $id);
    public function where($table, $proyection, $condition);
    public function update($table, $proyection, $id);
}
