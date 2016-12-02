<?php
class TableRefNode {
    var $table_alias;

    public function TableRefNode($table_alias) {
        $this->table_alias = $table_alias;
    }
}