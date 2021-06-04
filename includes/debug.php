<?php
/**
 * Fast Debug Mode to add message to WC_Logger
 *
 * @package Fast
 */

/**
 * Check if Fast debug mode is enabled.
 *
 * @return bool
 */
function fastwc_debug_mode_enabled() {
	$fastwc_debug_mode = get_option( FASTWC_SETTING_DEBUG_MODE, 0 );

	return ! empty( $fastwc_debug_mode );
}

/**
 * Log a message if Fast debug mode is enabled.
 *
 * @param string $level   WooCommerce log level. One of the following:
 *     'emergency': System is unusable.
 *     'alert': Action must be taken immediately.
 *     'critical': Critical conditions.
 *     'error': Error conditions.
 *     'warning': Warning conditions.
 *     'notice': Normal but significant condition.
 *     'info': Informational messages.
 *     'debug': Debug-level messages.
 * @param string $message Message to log.
 */
function fastwc_log( $level, $message ) {
	if ( ! fastwc_debug_mode_enabled() ) {
		return;
	}

	$logger = wc_get_logger();
	$logger->log( $level, $message, array( 'source' => 'fastwc' ) );
}

/**
 * Adds an emergency level message if Fast debug mode is enabled
 *
 * System is unusable.
 *
 * @see WC_Logger::log
 *
 * @param string $message Message to log.
 */
function fastwc_emergency( $message ) {
	fastwc_log( WC_Log_Levels::EMERGENCY, $message );
}

/**
 * Adds an alert level message if Fast debug mode is enabled.
 *
 * Action must be taken immediately.
 * Example: Entire website down, database unavailable, etc.
 *
 * @see WC_Logger::log
 *
 * @param string $message Message to log.
 */
function fastwc_alert( $message ) {
	fastwc_log( WC_Log_Levels::ALERT, $message );
}

/**
 * Adds a critical level message if Fast debug mode is enabled.
 *
 * Critical conditions.
 * Example: Application component unavailable, unexpected exception.
 *
 * @see WC_Logger::log
 *
 * @param string $message Message to log.
 */
function fastwc_critical( $message ) {
	fastwc_log( WC_Log_Levels::CRITICAL, $message );
}

/**
 * Adds an error level message if Fast debug mode is enabled.
 *
 * Runtime errors that do not require immediate action but should typically be logged
 * and monitored.
 *
 * @see WC_Logger::log
 *
 * @param string $message Message to log.
 */
function fastwc_error( $message ) {
	fastwc_log( WC_Log_Levels::ERROR, $message );
}

/**
 * Adds a warning level message if Fast debug mode is enabled.
 *
 * Exceptional occurrences that are not errors.
 *
 * Example: Use of deprecated APIs, poor use of an API, undesirable things that are not
 * necessarily wrong.
 *
 * @see WC_Logger::log
 *
 * @param string $message Message to log.
 */
function fastwc_warning( $message ) {
	fastwc_log( WC_Log_Levels::WARNING, $message );
}

/**
 * Adds a notice level message if Fast debug mode is enabled.
 *
 * Normal but significant events.
 *
 * @see WC_Logger::log
 *
 * @param string $message Message to log.
 */
function fastwc_notice( $message ) {
	fastwc_log( WC_Log_Levels::NOTICE, $message );
}

/**
 * Adds a info level message if Fast debug mode is enabled.
 *
 * Interesting events.
 * Example: User logs in, SQL logs.
 *
 * @see WC_Logger::log
 *
 * @param string $message Message to log.
 */
function fastwc_info( $message ) {
	fastwc_log( WC_Log_Levels::INFO, $message );
}

/**
 * Adds a debug level message if Fast debug mode is enabled.
 *
 * Detailed debug information.
 *
 * @see WC_Logger::log
 *
 * @param string $message Message to log.
 */
function fastwc_debug( $message ) {
	fastwc_log( WC_Log_Levels::DEBUG, $message );
}
