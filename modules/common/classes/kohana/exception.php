<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_Exception extends Kohana_Kohana_Exception 
{
    /**
	 * Inline exception handler, displays the error message, source of the
	 * exception, and the stack trace of the error.
	 *
	 * @uses    Kohana_Exception::text
	 * @param   object   exception object
	 * @return  boolean
	 */
	public static function handler(Exception $e)
	{
		try
		{
			// Get the exception information
			$type    = get_class($e);
			$code    = $e->getCode();
			$message = $e->getMessage();
			$file    = $e->getFile();
			$line    = $e->getLine();

			// Get the exception backtrace
			$trace = $e->getTrace();

			if ($e instanceof ErrorException)
			{
				if (isset(Kohana_Exception::$php_errors[$code]))
				{
					// Use the human-readable error name
					$code = Kohana_Exception::$php_errors[$code];
				}

				if (version_compare(PHP_VERSION, '5.3', '<'))
				{
					// Workaround for a bug in ErrorException::getTrace() that exists in
					// all PHP 5.2 versions. @see http://bugs.php.net/bug.php?id=45895
					for ($i = count($trace) - 1; $i > 0; --$i)
					{
						if (isset($trace[$i - 1]['args']))
						{
							// Re-position the args
							$trace[$i]['args'] = $trace[$i - 1]['args'];

							// Remove the args
							unset($trace[$i - 1]['args']);
						}
					}
				}
			}

			// Create a text version of the exception
			$error = Kohana_Exception::text($e);

			if (is_object(Kohana::$log))
			{
				// Add this exception to the log
				Kohana::$log->add(Log::ERROR, $error);

				$strace = Kohana_Exception::text($e)."\n--\n" . $e->getTraceAsString();
				Kohana::$log->add(Log::STRACE, $strace);

				// Make sure the logs are written
				Kohana::$log->write();
			}

			if (Kohana::$is_cli)
			{
				// Just display the text of the exception
				echo "\n{$error}\n";

				exit(1);
			}

			if ( ! headers_sent())
			{
				// Make sure the proper http header is sent
				$http_header_status = ($e instanceof HTTP_Exception) ? $code : 500;

				header('Content-Type: '.Kohana_Exception::$error_view_content_type.'; charset='.Kohana::$charset, TRUE, $http_header_status);
			}

			if (Request::$current !== NULL AND Request::current()->is_ajax() === TRUE)
			{
				// Just display the text of the exception
                $message = "<span style=\"color: #D34322;\">{$e->getMessage()}</span><br />
                    <b>URI:</b> ".Request::current()->controller()."/".Request::current()->action();
                    
				echo json_encode(array('html' => $message));

				exit(1);
			}

			// Start an output buffer
			ob_start();

			// Include the exception HTML
			if ($view_file = Kohana::find_file('views', Kohana_Exception::$error_view))
			{
				include $view_file;
			}
			else
			{
				throw new Kohana_Exception('Error view file does not exist: views/:file', array(
					':file' => Kohana_Exception::$error_view,
				));
			}

			// Display the contents of the output buffer
			echo ob_get_clean();

			exit(1);
		}
		catch (Exception $e)
		{
			// Clean the output buffer if one exists
			ob_get_level() and ob_clean();

			// Display the exception text
			echo Kohana_Exception::text($e), "\n";

			// Exit with an error status
			exit(1);
		}
	}
}
