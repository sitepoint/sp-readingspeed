<?php
/*
Plugin Name: Reading Speed
Plugin URI: http://www.sitepoint.com
Description: Calculate Reading Speed of an article
Version: 1.0
Author: Jude Aakjaer
Author URI: https://github.com/santouras
License: MIT
*/

namespace SitePoint;

class ReadingSpeed {
  /**
   * How fast we are reading in WPM
   * @var int
   **/
  const WORDS_PER_MINUTE = 200;

  static public function init() {
    // Action to save reading speed when a post is saved
    add_action( 'save_post', array( __CLASS__, 'setReadingSpeed' ), 10, 2 );
  }

  /**
   * Fetch our reading speed then save WPM in post meta
   *
   * @param $post_id int
   * @param $post object
   **/
  static function setReadingSpeed( $post_id, $post ) {
    update_post_meta($post->ID, "sp_reading_speed", ReadingSpeed::calculateReadingSpeed($post));
  }

  /**
   * Try to find our post_meta reading speed. If we can't find it, calculate,
   * save and return
   *
   * @param $post object
   * @return float
   **/
  static public function getReadingSpeed($post) {
    $reading_speed = get_post_meta($post->ID, "sp_reading_speed", true);

    if ($reading_speed == 0) {
      $reading_speed = ReadingSpeed::calculateReadingSpeed($post);
      update_post_meta($post->ID, "sp_reading_speed", $reading_speed);
    }

    return $reading_speed;
  }

  /**
   * Calculate Reading speed
   * Strip out all HTML and remove everything between and including shortcodes.
   * Shortcode content stripping is due to most shortcodes not being of the
   * content variety, and rather being image/code/pullquote/etc insertion, and
   * not really part of the main reading body
   *
   * @param $post object
   **/
  static public function calculateReadingSpeed($post) {
    $sanitized_body = strip_tags($post->post_content);
    $sanitized_body = preg_replace("/\[.*\]/", '', $sanitized_body);

    $read_time = str_word_count($sanitized_body) / self::WORDS_PER_MINUTE;

    return $read_time;
  }
}

ReadingSpeed::init();
