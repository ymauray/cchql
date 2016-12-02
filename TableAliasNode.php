<?php 

class TableAliasNode {
    var $table;
    var $alias;

    function TableAliasNode($table, $alias) {
        $this->table = $table;
        $this->alias = $alias;
    }
}