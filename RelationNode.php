<?php
class RelationNode {

    var $left_key;
    var $right_key;
    var $table_ref;

    public function RelationNode($left_key, $right_key, $table_ref) {
        $this->left_key = $left_key;
        $this->right_key = $right_key;
        $this->table_ref = $table_ref;
    }
}