grammar cchql2;

queries	:	query+
	;

query	:	MATCH relations where return_statement ';'
	;
	
relations
	:	table_ref relation*
	;
	
relation:	HYPHEN (OPEN_BRACKET ID? COLON ID? CLOSE_BRACKET HYPHEN)? table_ref
	;
	
table_ref
	:	OPEN_PAREN table_alias CLOSE_PAREN
	;
	
table_alias
	:	(ID COLON)? ID
	;
	
where	:	WHERE conditions
	;
	
conditions
	:	condition (cond_operator condition)*
	;
	
condition
	:	element rel_operator element
	|	element BETWEEN element AND element
	|	element is_or_is_not NULL
	|	OPEN_PAREN conditions CLOSE_PAREN
	|	NOT condition
	;

rel_operator
	:	EQ
	|	LTH
	|	GTH
	|	NOT_EQ
	|	LET
	|	GET
	|	CONTAINS
	;
	
element	:	col_ref
	|	ID
	|	INT
	|	FLOAT
	|	STRING
	;
	
cond_operator
	:	AND
	|	OR
	|	NOT
	;
	
contains:	col_ref CONTAINS STRING
	;
	
col_ref	:	ID DOT ID
	;
	
return_statement
	:	RETURN value_ref (COMMA value_ref)*
	;
	
value_ref
	:	ID (DOT ID)?
	;
	
is_or_is_not
	:	'IS'
	|	'IS NOT'
	;
	
MATCH	:	'MATCH'
	;
	
OPEN_PAREN
	:	'('
	;
	
CLOSE_PAREN
	:	')'
	;

COLON	:	':'
	;
	
HYPHEN	:	'-'
	;
	
OPEN_BRACKET
	:	'['
	;
	
CLOSE_BRACKET
	:	']'
	;
	
WHERE	:	'WHERE'
	;
	
AND	:	'AND'
	;
	
OR	:	'OR'
	;
	
NOT	:	'NOT'
	;
	
CONTAINS:	'CONTAINS'
	;
	
DOT	:	'.'
	;
	
RETURN	:	'RETURN'
	;
	
COMMA	:	','
	;

BETWEEN	:	'BETWEEN'
	;
	
NULL	:	'NULL'
	;
	
EQ	:	'='
	;
	
LTH	:	'<'
	;
	
GTH	:	'>'
	;
	
NOT_EQ	:	'<>'
	;
	
LET	:	'<='
	;
	
GET	:	'>='
	;
	
// ---------------------------------------------------------------------

ID  :	('a'..'z'|'A'..'Z'|'_') ('a'..'z'|'A'..'Z'|'0'..'9'|'_')*
    ;

INT :	'0'..'9'+
    ;

FLOAT
    :   ('0'..'9')+ '.' ('0'..'9')* EXPONENT?
    |   '.' ('0'..'9')+ EXPONENT?
    |   ('0'..'9')+ EXPONENT
    ;

COMMENT
    :   '//' ~('\n'|'\r')* '\r'? '\n' {$channel=HIDDEN;}
    |   '/*' ( options {greedy=false;} : . )* '*/' {$channel=HIDDEN;}
    ;

WS  :   ( ' '
        | '\t'
        | '\r'
        | '\n'
        ) {$channel=HIDDEN;}
    ;

CHAR:  '\'' ( ESC_SEQ | ~('\''|'\\') ) '\''
    ;

STRING
    :  '\'' ( ESC_SEQ | ~('\\'|'\'') )* '\''
    ;

fragment
EXPONENT : ('e'|'E') ('+'|'-')? ('0'..'9')+ ;

fragment
HEX_DIGIT : ('0'..'9'|'a'..'f'|'A'..'F') ;

fragment
ESC_SEQ
    :   '\\' ('b'|'t'|'n'|'f'|'r'|'\"'|'\''|'\\')
    |   UNICODE_ESC
    |   OCTAL_ESC
    ;

fragment
OCTAL_ESC
    :   '\\' ('0'..'3') ('0'..'7') ('0'..'7')
    |   '\\' ('0'..'7') ('0'..'7')
    |   '\\' ('0'..'7')
    ;

fragment
UNICODE_ESC
    :   '\\' 'u' HEX_DIGIT HEX_DIGIT HEX_DIGIT HEX_DIGIT
    ;

	