<?php
/*
Plugin Name: Quiz make plugin "sumire" 日本語クイズ作成プラグイン「すみれ」
Plugin URI: https://github.com/ayaoriko/wp-make-quiz
Description: You can create multiple-choice quizzes with shortcodes. ショートコードを使用して多肢選択式クイズを作成できます。
Version: 1.1
Author: Yoshihiko Yoshida's product was modified by ayaoriko.
Domain Path: /languages
*/

function wp_make_quiz_lib(){
  if(!is_admin()){
    wp_enqueue_style('wp_make_quiz', plugins_url('css/default.css', __FILE__));
    wp_enqueue_style('wp_make_quiz_school', plugins_url('css/school.css', __FILE__));
    wp_enqueue_script('wp_make_quiz', plugins_url('script/script.js', __FILE__), array(), false, true);
  }
}

add_action('wp_enqueue_scripts', 'wp_make_quiz_lib');
function wp_make_quiz_show_text($atts, $title = null){
  if(!isset($atts['question']) || !isset($atts['answer']) || $title == null ){
    return null;
  }

  //質問一覧
  $question_list = explode(',', $atts['question']);

  //質問一覧をシャッフル
  if(isset($atts['random']) && $atts['random'] == 'true'){
    shuffle($question_list);
  }

  if (!(empty($atts['style']))) {
  $style = $atts['style'];
  }else{
    $style = 'default';
  }

  if(!count($question_list)){
    return null;
  }

  $answer = $atts['answer'];

  //HTML生成
  $html = '<div class="wp-make-quiz '.  $style.' ">';
  $html .= '<div class="wp-make-quiz-title"><p>'.$title.'</p></div>';
  $html .= '<ul class="wp-make-quiz-question">';
  foreach ($question_list as $question){
    if($question == $answer){
      $html .= '<li class="wp-make-quiz-question-list correct">'.$question.'</li>';
    }else{
      $html .= '<li class="wp-make-quiz-question-list incorrect">'.$question.'</li>';
    }
  }
  $html .= '</ul>';

  $html .= '<div class="wp-make-quiz-answer">';
  $html .= '<div class="wp-make-quiz-result-wrap">';
  $html .= '<p class="wp-make-quiz-result correct"><span>正解！</span></p>';
  $html .= '<p class="wp-make-quiz-result incorrect"><span>不正解...</span></p>';
  $html .= '<p class="wp-make-quiz-result-text">正解は「<span>'.$answer.'</span>」</p>';
  $html .= '</div>';
  if(isset($atts['commentary']) && $atts['commentary'] !=''){
    $html .= '<div class="wp-make-quiz-commentary"><p>'.$atts['commentary'].'</p></div>';
  }
  $html .= '<div class="wp-make-quiz-continue-wrap"><p class="wp-make-quiz-continue">問題に戻る</p></div>';
  $html .= '</div>';
  $html .= '</div>';

  return $html;
}
add_shortcode('quiz', 'wp_make_quiz_show_text');


/**
 * ショートコード設定
 * **/

add_filter( 'mce_external_plugins', 'add_add_shortcode_button_plugin' );
function add_add_shortcode_button_plugin( $plugin_array ) {
  $plugin_array[ 'wp_make_quiz_button_plugin' ] = plugins_url('script/quicktag.js', __FILE__);
  return $plugin_array;
}

add_filter( 'mce_buttons', 'add_shortcode_button' );
function add_shortcode_button( $buttons ) {
  $buttons[] = 'quiz';
  return $buttons;
}

add_action( 'admin_print_footer_scripts', 'add_shortcode_quicktags' );
function add_shortcode_quicktags() {
  if ( wp_script_is('quicktags') ) {
?>
  <script>
    QTags.addButton( 'wp_make_quiz', '[quiz]', '[quiz]', '[/quiz]' );
  </script>
<?php
  }
}