<?php

namespace WC_CUSTOM_LOGO;

class IMAGE_UTIL extends WC_BASE{

  function _isJSON($string) {
		return ((is_string($string) && (is_object(json_decode($string)) || is_array(json_decode($string))))) ? true : false;
	}

  function removebg( $imgUrl ){

    $wp_upload_dir = wp_upload_dir();

		$fileData = pathinfo( $imgUrl );
		$tmp_file_name = '/wc' . time() . '.png';
		$tmp_img = $wp_upload_dir[ 'path' ] . $tmp_file_name;

		$Remove_BG_api_key = get_option( 'wc_settings_tab_custom_logo_api_key' );
		$body = array(
			'image_url' => $imgUrl,
			'size'			=> 'auto',
      'format'    => 'png'
		);
		$argRemotePost = [
			'body' => $body,
			'headers' => [ 'X-Api-Key' => $Remove_BG_api_key ]
		];

		$response = wp_remote_post(  'https://api.remove.bg/v1.0/removebg', $argRemotePost );

		if ( $this->_isJSON( $response['body'] ) ) {
			$errorResponseArray = json_decode( $response['body'] );
      //print_r( $errorResponseArray );
			return $errorResponseArray;
		}


		$er = array();

		if( !$fp = fopen( $tmp_img, "wb" ) ) {
			$er['errors_msg'][0]['title'] = __( 'Check uploads folder permissions', 'wc-remove-bg' );
			return $er;
		}

		if(fwrite( $fp, $response['body'] ) === FALSE){
			$er['errors_msg'][0]['title'] = __( 'Check uploads folder permissions', 'wc-remove-bg' );
			return $er;
		}
		if(fclose( $fp ) === FALSE){
			$er['errors_msg'][0]['title'] = __( 'Check available disk space', 'wc-remove-bg' );
			return $er;
		}

		$attachment = array(
			'guid'           => $tmp_img,
			'post_mime_type' => mime_content_type( $tmp_img ),
			'post_title'     => pathinfo( $tmp_img )['filename'],
			'post_content'   => '',
			'post_status'    => 'inherit'
		);

		$attach_id = wp_insert_attachment( $attachment, $tmp_img );
    return $this->getAttachmentSrc( $attach_id );

  }

  function getAttachmentSrc( $attachment_id ){
    $attachment_src = wp_get_attachment_image_src( $attachment_id, 'full' );
    if( is_array( $attachment_src ) && count( $attachment_src ) ){
      return $attachment_src[0];
    }
    return '';
  }

}
