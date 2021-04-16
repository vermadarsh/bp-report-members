<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit, if accessed directly

// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Bp_Flagged_Members_List extends WP_List_Table {
	/**
	 * Prepare the items for the table to process
	 *
	 * @return Void
	 */
	public function prepare_items() {
		$columns = $this->get_columns();
		$hidden = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();
		$data = $this->table_data();

		// usort( $data, array( &$this, 'sort_data' ) );

		$perPage = 10;
		$currentPage = $this->get_pagenum();
		$totalItems = count($data);

		$this->set_pagination_args(
			array(
				'total_items'	=>	$totalItems,
				'per_page'		=>	$perPage
			)
		);

		$data = array_slice( $data, ( ( $currentPage - 1 ) * $perPage ), $perPage );
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items = $data;
	}

	/**
	 * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @return Array
	 */
	public function get_columns() {
		
		$columns = array(
			'title'				=>	__( 'Title', BPFM_TEXT_DOMAIN ),
			'flagged_member'	=>	__( 'Flagged Member', BPFM_TEXT_DOMAIN ),
			'flagged_count'		=>	__( 'Flagged Count', BPFM_TEXT_DOMAIN ),
			'date'				=>	__( 'Last Flagged', BPFM_TEXT_DOMAIN ),
			'status'			=>	__( 'Status', BPFM_TEXT_DOMAIN ),
			'actions'			=>	__( 'Actions', BPFM_TEXT_DOMAIN )
		);
		return $columns;

	}

	/**
	 * Define which columns are hidden
	 *
	 * @return Array
	 */
	public function get_hidden_columns() {

		return array();

	}

	/**
	* Get the table data
	*
	* @return Array
	*/
	private function table_data() {
		
		global $bp_flag_members;
		$flags = get_posts(
			array(
				'post_type'		=>	$bp_flag_members->cpt_slug,
				'post_status'	=>	'publish',
				'posts_per_page'=>	-1,
				'order_by'		=>	'modified',
				'order'			=>	'DESC'
			)
		);

		$data = array();
		if( ! empty( $flags ) ) {
			foreach( $flags as $flag ) {
				$flag_id = $flag->ID;
				/**
				 * Flagged Member
				 */
				$flagged_member = $flag->post_parent;
				$flagged_member_data = get_userdata( $flagged_member );
				$member_fn = xprofile_get_field_data( 1, $flagged_member );
				$member_ln = xprofile_get_field_data( 19, $flagged_member );
				
				if( $member_fn != '' && $member_ln != '' ) {
					$flagged_membername = $member_fn . ' ' . $member_ln;
				} elseif( $member_fn != '' && $member_ln == '' ) {
					$flagged_membername = $member_fn;
				} else {
					$flagged_membername = $flagged_member_data->data->user_login;
				}
				$flagged_by_member_link = '<a href="' . bp_core_get_user_domain( $flagged_member ) . '" target="_blank">' . $flagged_membername . '</a>';

				/**
				 * Flagged Count
				 */
				$flagged_dates = get_post_meta( $flag_id, '_bpfm_flagged_date', true );
				$flagged_count = count( $flagged_dates );

				/**
				 * Last Flagged On
				 */
				$flagged_dates = array_reverse( $flagged_dates );
				$last_flagged_on = date( 'F j, Y, g:i A', strtotime( $flagged_dates[0] ) );
				
				$status = get_post_meta( $flag_id, '_bpfm_flag_status', true );
				$status_label = ! empty( $status ) ? $bp_flag_members->flag_statuses[ $status ] : '';

				$status_action = get_post_meta( $flag_id, '_bpfm_flag_status_action', true );
				if( $status_action != '' ) {
					$status_label .= '<br/><small>(' . $bp_flag_members->flag_status_actions[ $status_action ] . ')</small>';
				}

				$actions = '';
				$actions .= '<div class="bpfm-flag-actions">';
				$actions .= '<button type="button" class="button button-secondary bpfm-delete-flag" data-fid="' . $flag_id . '"><span><i class="fa fa-trash" aria-hidden="true"></i></span></button>';
				$actions .= '<button type="button" class="button button-secondary bpfm-view-flag" data-fid="' . $flag_id . '" data-ftitle="' . $flag->post_title . '"><span><i class="fa fa-eye" aria-hidden="true"></i></span></button>';

				// If the status is banned
				if( $status != '' && $status == 'approved' && $status_action != '' && $status_action == 'banned' ) {
					$user_edit_url = get_edit_user_link( $flagged_member ) . '#bpfm-unban-member';
					$actions .= '<button type="button" class="button button-secondary bpfm-unban-member" data-url="' . $user_edit_url . '"><span><i class="fa fa-ban" aria-hidden="true"></i></span></button>';
				}

				$actions .= '</div>';

				$data[] = array(
					'title'				=>	$flag->post_title,
					'flagged_member'	=>	! empty( $flagged_member ) ? $flagged_by_member_link : '',
					'flagged_count'		=>	$flagged_count,
					'date'				=>	$last_flagged_on,
					'status'			=>	$status_label,
					'actions'			=>	$actions,
				);
			}
		}
		return $data;

	}

	/**
	 * Message to be displayed when there are no items
	 *
	 * @since 3.1.0
	 */
	public function no_items() {

		_e( 'No Flagged Members Found.', BPFM_TEXT_DOMAIN );
		
	}

	/**
	 * Define what data to show on each column of the table
	 *
	 * @param  Array $item        Data
	 * @param  String $column_name - Current column name
	 *
	 * @return Mixed
	 */
	public function column_default( $item, $column_name ) {
		
		switch( $column_name ) {
			case 'title':
			case 'flagged_member':
			case 'flagged_count':
			case 'status':
			case 'date':
			case 'actions':
			return $item[ $column_name ];

			default:
			return print_r( $item, true ) ;
		}

	}
}