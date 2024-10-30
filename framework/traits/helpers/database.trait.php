<?php

trait cjwpbldr_helpers_database {

	public function wpTableName( $table ) {
		global $wpdb;

		return $wpdb->prefix . $table;
	}

	protected function dbInfo() {
		global $wpdb;

		return [
			'database_type' => 'mysql',
			'database_name' => DB_NAME,
			'server' => DB_HOST,
			'username' => DB_USER,
			'password' => DB_PASSWORD,
			'charset' => 'utf8',
			'prefix' => $wpdb->prefix,
		];
	}

	public function uniqueColumnValue( $string, $table, $column, $separator = '-' ) {
		global $wpdb;
		$table = ( ! $this->containString( $wpdb->prefix, $table )) ? $wpdb->prefix . $table : $table;
		$string = preg_replace( "/-$/", "", preg_replace( '/[^a-z0-9]+/i', $separator, $string ) );
		$query = $wpdb->get_row( "SELECT * FROM {$table} WHERE {$column} = '{$string}'", ARRAY_N );

		if( is_null( $query ) ) {
			$query = [];
		}

		if( is_array( $query ) && count( $query ) == 0 ) {
			return $string;
		} else {
			preg_match( '/(.+)-([0-9]+)$/', $string, $match );
			$new_string = isset( $match[2] ) ? $match[1] . '-' . ($match[2] + 1) : $string . '-' . count( $query );
			if( count( $query ) == 0 ) {
				return $new_string;
			} else {
				return $this->uniqueColumnValue( $new_string, $table, $column );
			}
		}
	}

	public function getTableColumns( $table, $exclude = [] ) {
		global $wpdb;
		$table_name = $wpdb->prefix . $table;
		$existing_columns = $wpdb->get_col( "DESC {$table_name}", 0 );
		$return = [];
		if( is_array( $existing_columns ) && ! empty( $existing_columns ) ) {
			foreach( $existing_columns as $column ) {
				$return[ $column ] = $column;
			}
		}

		if( is_array( $exclude ) && ! empty( $exclude ) ) {
			foreach( $exclude as $key => $exclude_col ) {
				unset( $return[ $exclude_col ] );
			}
		}

		return $return;
	}

	public function tableExists( $table_name ) {
		global $wpdb;

		return ($wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name);
	}

	public function dbUpdate( $table, $data, $id_field, $id_value ) {
		global $wpdb;
		$table = $this->wpTableName( $table );
		foreach( $data as $field => $value ) {
			$fields[] = sprintf( "`%s` = '%s'", $field, esc_sql( $value ) );
		}
		$field_list = join( ',', $fields );
		$query = sprintf( "UPDATE `%s` SET %s WHERE `%s` = %s", $table, $field_list, $id_field, intval( $id_value ) );
		$wpdb->query( $query );

		return intval( $id_value );
	}

	public function dbInsert( $table, $data ) {
		global $wpdb;
		$table = $this->wpTableName( $table );
		foreach( $data as $field => $value ) {
			$value = (is_array( $value )) ? serialize( $value ) : $value;
			$fields[] = '`' . esc_sql( $field ) . '`';
			$values[] = "'" . esc_sql( $value ) . "'";
		}
		$field_list = join( ',', $fields );
		$value_list = join( ', ', $values );
		$query = "INSERT INTO `" . $table . "` (" . $field_list . ") VALUES (" . $value_list . ")";
		$wpdb->query( $query );

		return $wpdb->insert_id;
	}

	public function dbSelect( $table, $columns = '*', $where = [], $limit = null, $offset = 0 ) {
		global $wpdb;
		$query_string = [];
		$columns = (is_array( $columns )) ? implode( ', ', $columns ) : $columns;
		$table_name = $this->wpTableName( $table );
		$query_string[] = "SELECT $columns FROM $table_name ";
		if( is_array( $where ) && ! empty( $where ) ) {
			$query_string[] = $this->whereClause( $where );
		}
		if( is_string( $where ) ) {
			$query_string[] = 'WHERE';
			$query_string[] = $where;
		}
		$query_string = implode( ' ', $query_string );
		if( ! is_null( $limit ) ) {
			$query_string = $query_string . " LIMIT {$offset}, {$limit}";
		}

		return $wpdb->get_results( $query_string, 'ARRAY_A' );
	}

	public function dbGet( $table, $columns = '*', $where = [] ) {
		global $wpdb;
		$query_string = [];
		$table_name = $this->wpTableName( $table );
		$query_string[] = "SELECT $columns FROM $table_name ";
		if( is_array( $where ) && ! empty( $where ) ) {
			$query_string[] = $this->whereClause( $where );
		}
		if( is_string( $where ) ) {
			$query_string[] = 'WHERE';
			$query_string[] = $where;
		}
		$query_string = implode( ' ', $query_string );
		$return = $wpdb->get_row( $query_string, 'ARRAY_A' );

		return ( ! is_null( $return )) ? $return : false;
	}

	public function dbDelete( $table, $where = [] ) {
		global $wpdb;
		$query_string = [];
		$table_name = $this->wpTableName( $table );
		$query_string[] = "DELETE FROM $table_name ";
		if( is_array( $where ) && ! empty( $where ) ) {
			$query_string[] = $this->whereClause( $where );
		}
		if( ! is_array( $where ) ) {
			$query_string[] = $where;
		}
		$query_string = implode( ' ', $query_string );

		$return = $wpdb->query( $query_string );

		return ( ! is_null( $return )) ? $return : false;
	}

	public function dbCount( $table, $field, $where = [] ) {
		global $wpdb;
		$query_string = [];
		$table_name = $this->wpTableName( $table );
		$query_string[] = "SELECT COUNT($field) FROM $table_name ";
		if( is_array( $where ) && ! empty( $where ) ) {
			$query_string[] = $this->whereClause( $where );
		}
		$query_string = implode( ' ', $query_string );
		$return = (int) $wpdb->get_var( $query_string );

		return $return;
	}

	public function whereClause( $where ) {

		$operator = '=';

		$return[] = 'WHERE';
		$exclude_operators = ['AND', 'OR'];
		$count = 0;
		foreach( $where as $key => $value ) {
			$count ++;
			if( $count > 1 ) {
				$return[] = 'AND';
			}
			if( ! in_array( $key, $exclude_operators ) ) {
				if( $this->containString( '[', $key ) ) {
					$key = str_replace( ['[', ']'], ' ', $key );
					$operator = '';
				}
				$return[] = $key . $operator . "'$value'";
			}
		}

		if( isset( $where['AND'] ) ) {
			$count = 0;
			foreach( $where['AND'] as $key => $value ) {
				$count ++;
				if( $count > 1 ) {
					$return[] = 'AND';
				}
				if( $this->containString( '[', $key ) ) {
					$key = str_replace( ['[', ']'], ' ', $key );
					$operator = '';
				}
				$return[] = $key . $operator . "'$value'";
			}
		}

		if( isset( $where['OR'] ) ) {
			$count = 0;
			foreach( $where['OR'] as $key => $value ) {
				$count ++;
				if( $count > 1 ) {
					$return[] = 'OR';
				}
				if( $this->containString( '[', $key ) ) {
					$key = str_replace( ['[', ']'], ' ', $key );
					$operator = '';
				}
				$return[] = $key . $operator . "'$value'";
			}
		}
		$return = implode( ' ', $return );

		return $return;
	}

}