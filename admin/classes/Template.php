<?php
/**
 * Class to render templates.
 *
 * @package plugin-boilerplate
 */

namespace BoilerplatePluginName\Admin;

/** Prevent direct access */
if ( ! function_exists( 'add_action' ) ) {
	header( 'HTTP/1.0 401 Unauthorized' );
	exit;
}

/**
 * Class Template
 *
 * @since 1.0.0
 */
final class Template {

	/**
	 * Method to render template
	 *
	 * @param array $template template name and args, use keys 'template' to set template name and ...args to pass another values.
	 * @param bool  $is_admin verify if is admin template.
	 *
	 * @usage $template = array( 'template' => 'template-name.php', 'args' => array( 'var' => 'value' ) ); Template::render( $template );
	 * @return void
	 * @since 1.0.0
	 */
	public static function render( array $template, bool $is_admin = false ): void {
		$path = 'public';
		if ( $is_admin ) {
			$path = 'admin';
		}
		$file_path = PluginInit::getPluginDirPath() . "$path/templates/" . $template['template'];
		$output    = __( 'Template not found', 'plugin-name' );
		if ( file_exists( $file_path ) ) {
			ob_start();
			require $file_path;
			$output = ob_get_clean();
		}

		echo wp_kses( $output, self::sanitizeOutput() );
	}

	/**
	 * Sanitize html output
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function sanitizeOutput(): array {
		$allowed_tags = [
			'div'      => [
				'class'        => [],
				'id'           => [],
				'role'         => [],
				'aria-label'   => [],
				'aria-pressed' => [],
			],
			'table'    => [
				'class' => [],
				'id'    => [],
			],
			'thead'    => [
				'class' => [],
				'id'    => [],
			],
			'tr'       => [
				'class' => [],
				'id'    => [],
			],
			'td'       => [
				'class'   => [],
				'id'      => [],
				'colspan' => [],
			],
			'th'       => [ 'scope' => [] ],
			'caption'  => [ 'class' => [] ],
			'tbody'    => [
				'class' => [],
				'id'    => [],
			],
			'tfoot'    => [
				'class' => [],
				'id'    => [],
			],
			'a'        => [
				'href'         => [],
				'title'        => [],
				'class'        => [],
				'id'           => [],
				'target'       => [],
				'data-user-id' => [],
				'aria-label'   => [],
			],
			'p'        => [
				'class' => [],
				'id'    => [],
			],
			'hr'       => [],
			'ul'       => [
				'class' => [],
				'id'    => [],
			],
			'li'       => [
				'label' => [],
				'class' => [],
			],
			'h1'       => [
				'class' => [],
				'id'    => [],
			],
			'h2'       => [
				'class' => [],
				'id'    => [],
			],
			'h3'       => [
				'class' => [],
				'id'    => [],
			],
			'h4'       => [
				'class' => [],
				'id'    => [],
			],
			'u'        => [],
			'i'        => [
				'class' => [],
				'id'    => [],
			],
			'link'     => [
				'rel'  => [],
				'href' => [],
				'id'   => [],
			],
			'small'    => [],
			'pre'      => [],
			'br'       => [],
			'img'      => [
				'alt'   => [],
				'src'   => [],
				'class' => [],
				'id'    => [],
			],
			'strong'   => [
				'class' => [],
				'id'    => [],
			],
			'span'     => [
				'class'          => [],
				'id'             => [],
				'data-role-id'   => [],
				'data-role-name' => [],
				'data-user-id'   => [],
				'title'          => [],
			],
			'form'     => [
				'method' => [],
				'action' => [],
				'class'  => [],
				'id'     => [],
			],
			'label'    => [
				'for'   => [],
				'class' => [],
				'id'    => [],
			],
			'input'    => [
				'type'          => [],
				'name'          => [],
				'value'         => [],
				'id'            => [],
				'class'         => [],
				'required'      => [],
				'checked'       => [],
				'placeholder'   => [],
				'title'         => [],
				'autocomplete'  => [],
				'aria-expanded' => [],
				'aria-owns'     => [],
				'style'         => [],
			],
			'select'   => [
				'name'     => [],
				'class'    => [],
				'id'       => [],
				'required' => [],
				'onchange' => [],
			],
			'textarea' => [
				'name'  => [],
				'class' => [],
				'id'    => [],
				'rows'  => [],
				'cols'  => [],
			],
			'option'   => [
				'value'    => [],
				'selected' => [],
			],
			'button'   => [
				'type'      => [],
				'class'     => [],
				'id'        => [],
				'role'      => [],
				'hidefocus' => [],
			],
		];

		return apply_filters( 'plugin_name_sanitize_output', $allowed_tags );
	}
}
