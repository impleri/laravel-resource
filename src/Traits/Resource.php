<?php namespace Impleri\Resource\Traits;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Collection;

/**
 * Base Resource Trait
 *
 * Provides some simple actions to map
 */
trait Resource
{
    /**
     * The default output format to use.
     * @var string
     */
    protected $defaultFormat = 'json';

    /**
     * Get Format
     *
     * Determines which format to use for output.
     */
    protected function getFormat()
    {
        $format = $this->defaultFormat;

        if (!Request::wantsJson()) {
            $format = Request::format($format);
        }

        return $format;
    }

    /**
     * Respond
     *
     * Generates a response according to the detected format.
     * @param  mixed   $data    Data to pass to response
     * @param  string  $view    Full name of view
     * @param  integer $status  HTTP status code
     * @param  array   $headers Headers to pass to response
     * @return \Illuminate\Http\Response   Laravel response
     */
    protected function respond($data, $view = '', $status = 200, $headers = array())
    {
        $format = $this->getFormat();
        switch ($format) {
            case 'xml':
            case 'txt':
                /* Ensure resource views are in an expected directory.
                 * @example The request is for a blog post in XML format. The
                 * view name should be the default path for html rendering (e.g.
                 * "post.show"). The final path will be resources.xml.post.show.
                 * This is to keep API-aimed views separate from standard HTML
                 * ones.
                 */
                if (!empty($view)) {
                    if (strpos($view, $format) === false) {
                        $view = $format . '.' . $view;
                    }

                    if (strpos($view, 'resources') === false) {
                        $view = 'resources.' . $view;
                    }
                }
                // fall through to html handling

            case 'html':
                if (!empty($view)) {
                    $response = Response::view($view, $data, $status, $headers);
                } else {
                    $response = Response::make(
                        Collection::make($data)->flatten()->implode(0, "\n"),
                        $status,
                        $headers
                    );
                }
                break;

            default:
                $json = (isset($data['json'])) ? $data['json'] : $data;
                $response = Response::json($json, $status, $headers);
                break;
        }

        return $response;
    }

    /**
     * Not Supported
     *
     * Helper method to return an error for unsupported methods.
     * @param  string $view The view to render
     * @return \Illuminate\Http\Response Laravel response
     */
    protected function notSupported($view = 'errors.405')
    {
        $data = array(
            'success' => false,
            'errors' => array(
                'This resource does not support this method'
            ),
        );
        return $this->respond($data, $view, 405);
    }
}
