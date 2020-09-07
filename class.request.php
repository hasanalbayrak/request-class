<?php

namespace Hasan\App;

class Request
{

    private $redirection;
    private $method;
    private $name;
    private $title;

    /**
     * @return mixed
     */
    public function getRedirection()
    {
        return $this->redirection;
    }

    /**
     * @param mixed $redirection
     */
    public function setRedirection($redirection)
    {
        $this->redirection = $redirection;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @param $session
     * @return mixed
     */
    public function getSession($session)
    {
        return $_SESSION[$session];
    }

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    public function setSession($name, $value)
    {
        return $_SESSION[$name] = $value;
    }

    /**
     * @param $json
     * @param null $options
     * @return false|string
     */
    public function json($json, $options = NULL)
    {
        if ($options != NULL) {
            return json_encode($json, $options);
        }
        echo json_encode($json);
        return true;
    }

    /**
     * @param $title
     * @return string
     */
    private function page_title($title)
    {
        $this->assign("page_title", $title." - ".$this->getTitle());
        return $title." - ".$this->getTitle();
    }

    public function display($templatefile)
    {
        global $Smarty;
        $this->assign("templatefile", $templatefile);
        return $Smarty->display($templatefile.".tpl");
    }

    /**
     * @param $template_file
     * @param $page_title
     * @param array $assigns
     * @return string|void
     */
    public function view($template_file, $page_title, $assigns = [])
    {
        $this->page_title($page_title);
        foreach ($assigns as $key => $val) {
            $this->assign($key, $val);
        }
        return $this->display($template_file);
    }

    /**
     * @param $smarty_variable
     * @param $var
     * @return \Smarty
     */
    public function assign($smarty_variable, $var)
    {
        global $Smarty;
        return $Smarty->assign($smarty_variable, $var);
    }

    /**
     * @param null $session
     * @return array|mixed
     */
    public function session($session = NULL)
    {
        return $this->request($session, 'SESSION');
    }

    /**
     * @param null $request
     * @param string $type
     * @param null $options
     * @return mixed
     */
    public function request($request = NULL, $type = "POST", $options = NULL)
    {

        switch ($type) {

            case 'GET':
                $result = $_GET;
                break;

            case 'FILES':
                $result = $_FILES;
                break;

            case 'SERVER':
                $result = $_SERVER;
                break;

            case 'SESSION':
                $result = $_SESSION;
                break;

            default:
                if ($options == "isset") {
                    $result = isset($_POST);
                }else{
                    $result = $_POST;
                }
                break;
        }

        if ($request) {
            $result = $result[$request];
        }

        return $result;
    }

    /**
     * @param bool $is_referer
     */
    public function redirect($is_referer = false)
    {
        if ($is_referer) {
            $array1 = explode("/", server("HTTP_REFERER"));
            $queries = end($array1);
            $this->setRedirection($queries);
        }
        return Header("Location: " . $this->getRedirection());
    }

    /**
     * @return bool|void
     */
    public function authorization()
    {
        if (!$this->request($this->getName(), $this->getMethod())) {
            $this->setRedirection("clientarea.php");
            return $this->redirect();
        }else{
            return true;
        }
    }

    /**
     * @param $header
     */
    public function header($header) {
        return Header($header);
    }


    /**
     * @param string $type
     * @return int
     */
    public function status($type = "OK")
    {
        switch ($type) {

            /**
             * Source: https://restfulapi.net/http-status-codes/
             *
             *
             * It indicates that the REST API successfully carried out whatever
             * action the client requested and that no more specific code in
             * the 2xx series is appropriate.
             *
             * Unlike the 204 status code, a 200 response should include a response body.
             * The information returned with the response is dependent on the
             * method used in the request, for example:
             *
             * GET an entity corresponding to the requested resource is sent in the response;
             * HEAD the entity-header fields corresponding to the requested resource are sent
             *      in the response without any message-body;
             * POST an entity describing or containing the result of the action;
             * TRACE an entity containing the request message as received by the end server.
             */
            case 'OK':
                $code = 200;
                break;

            /**
             * A REST API responds with the 201 status code whenever a resource is created inside
             * a collection. There may also be times when a new resource is created as a result of
             * some controller action, in which case 201 would also be an appropriate response.
             *
             * The newly created resource can be referenced by the URI(s) returned in the entity of
             * the response, with the most specific URI for the resource given by a Location header field.
             *
             * The origin server MUST create the resource before returning the 201 status code.
             * If the action cannot be carried out immediately, the server SHOULD respond with a
             * 202 (Accepted) response instead.
             */
            case 'Created':
                $code = 201;
                break;

            /**
             * A 202 response is typically used for actions that take a long while to process.
             * It indicates that the request has been accepted for processing, but the processing
             * has not been completed. The request might or might not be eventually acted upon,
             * or even maybe disallowed when processing occurs.
             *
             * Its purpose is to allow a server to accept a request for some other process
             * (perhaps a batch-oriented process that is only run once per day) without requiring
             * that the user agent’s connection to the server persist until the process is completed.
             *
             * The entity returned with this response SHOULD include an indication of the request’s
             * current status and either a pointer to a status monitor (job queue location) or some
             * estimate of when the user can expect the request to be fulfilled.
             */
            case 'Accepted':
                $code = 202;
                break;

            /**
             * The 204 status code is usually sent out in response to a PUT, POST, or DELETE
             * request when the REST API declines to send back any status message or representation
             * in the response message’s body.
             *
             * An API may also send 204 in conjunction with a GET request to indicate that the
             * requested resource exists, but has no state representation to include in the body.
             *
             * If the client is a user agent, it SHOULD NOT change its document view from that which
             * caused the request to be sent. This response is primarily intended to allow input for
             * actions to take place without causing a change to the user agent’s active document view,
             * although any new or updated metainformation SHOULD be applied to the document currently
             * in the user agent’s active view.
             *
             * The 204 response MUST NOT include a message-body and thus is always terminated by the
             * first empty line after the header fields.
             */
            case 'No Content':
                $code = 204;
                break;


            /**
             * The 301 status code indicates that the REST API’s resource model has been significantly
             * redesigned, and a new permanent URI has been assigned to the client’s requested resource.
             * The REST API should specify the new URI in the response’s Location header, and all future
             * requests should be directed to the given URI.
             *
             * You will hardly use this response code in your API as you can always use the API versioning
             * for new API while retaining the old one.
             */
            case 'Moved Permanently':
                $code = 301;
                break;

            /**
             * The HTTP response status code 302 Found is a common way of performing URL redirection.
             * An HTTP response with this status code will additionally provide a URL in the Location
             * header field. The user agent (e.g., a web browser) is invited by a response with this
             * code to make a second. Otherwise identical, request to the new URL specified in the location field.
             *
             * Many web browsers implemented this code in a manner that violated this standard, changing the
             * request type of the new request to GET, regardless of the type employed in the original
             * request (e.g., POST). RFC 1945 and RFC 2068 specify that the client is not allowed to
             * change the method on the redirected request. The status codes 303 and 307 have been added for
             * servers that wish to make unambiguously clear which kind of reaction is expected of the client.
             */
            case 'Found':
                $code = 302;
                break;

            /**
             * A 303 response indicates that a controller resource has finished its work, but instead of
             * sending a potentially unwanted response body, it sends the client the URI of a response
             * resource. The response can be the URI of the temporary status message, or the URI to some
             * already existing, more permanent, resource.
             *
             * Generally speaking, the 303 status code allows a REST API to send a reference to a resource
             * without forcing the client to download its state. Instead, the client may send a GET request
             * to the value of the Location header.
             *
             * The 303 response MUST NOT be cached, but the response to the second (redirected) request
             * might be cacheable
             */
            case 'See Other':
                $code = 303;
                break;

            /**
             * This status code is similar to 204 (“No Content”) in that the response body must be empty.
             * The critical distinction is that 204 is used when there is nothing to send in the body,
             * whereas 304 is used when the resource has not been modified since the version specified
             * by the request headers If-Modified-Since or If-None-Match.
             *
             * In such a case, there is no need to retransmit the resource since the client still has a
             * previously-downloaded copy.
             *
             * Using this saves bandwidth and reprocessing on both the server and client, as only the header
             * data must be sent and received in comparison to the entirety of the page being re-processed
             * by the server, then sent again using more bandwidth of the server and client.
             */
            case 'Not Modified':
                $code = 304;
                break;

            /**
             * A 307 response indicates that the REST API is not going to process the client’s request.
             * Instead, the client should resubmit the request to the URI specified by the response message’s
             * Location header. However, future requests should still use the original URI.
             *
             * A REST API can use this status code to assign a temporary URI to the client’s requested resource.
             * For example, a 307 response can be used to shift a client request over to another host.
             *
             * The temporary URI SHOULD be given by the Location field in the response. Unless the request
             * method was HEAD, the entity of the response SHOULD contain a short hypertext note with a
             * hyperlink to the new URI(s). If the 307 status code is received in response to a request
             * other than GET or HEAD, the user agent MUST NOT automatically redirect the request unless
             * it can be confirmed by the user, since this might change the conditions under which the
             * request was issued.
             */
            case 'Temporary Redirect':
                $code = 307;
                break;

            /**
             * 400 is the generic client-side error status, used when no other 4xx error code is appropriate.
             * Errors can be like malformed request syntax, invalid request message parameters, or deceptive
             * request routing etc.
             *
             * The client SHOULD NOT repeat the request without modifications.
             */
            case 'Bad Request':
                $code = 400;
                break;

            /**
             * A 401 error response indicates that the client tried to operate on a protected resource
             * without providing the proper authorization. It may have provided the wrong credentials or
             * none at all. The response must include a WWW-Authenticate header field containing a
             * challenge applicable to the requested resource.
             *
             * The client MAY repeat the request with a suitable Authorization header field. If the
             * request already included Authorization credentials, then the 401 response indicates
             * that authorization has been refused for those credentials. If the 401 response contains
             * the same challenge as the prior response, and the user agent has already attempted
             * authentication at least once, then the user SHOULD be presented the entity that was
             * given in the response, since that entity might include relevant diagnostic information.
             */
            case 'Unauthorized':
                $code = 401;
                break;

            /**
             * A 403 error response indicates that the client’s request is formed correctly, but the
             * REST API refuses to honor it, i.e. the user does not have the necessary permissions for
             * the resource. A 403 response is not a case of insufficient client credentials; that
             * would be 401 (“Unauthorized”).
             *
             * Authentication will not help, and the request SHOULD NOT be repeated. Unlike a 401
             * Unauthorized response, authenticating will make no difference.
             */
            case 'Forbidden':
                $code = 403;
                break;

            /**
             * The 404 error status code indicates that the REST API can’t map the client’s URI
             * to a resource but may be available in the future. Subsequent requests by
             * the client are permissible.
             *
             * No indication is given of whether the condition is temporary or permanent.
             * The 410 (Gone) status code SHOULD be used if the server knows, through some
             * internally configurable mechanism, that an old resource is permanently unavailable
             * and has no forwarding address. This status code is commonly used when the server
             * does not wish to reveal exactly why the request has been refused, or when
             * no other response is applicable.
             */
            case 'Not Found':
                $code = 404;
                break;

            /**
             * The API responds with a 405 error to indicate that the client tried to use an
             * HTTP method that the resource does not allow. For instance, a read-only resource
             * could support only GET and HEAD, while a controller resource might allow GET and POST,
             * but not PUT or DELETE.
             */
            case 'Method Not Allowed':
                $code = 405;
                break;

            /**
             * The 406 error response indicates that the API is not able to generate any of the
             * client’s preferred media types, as indicated by the Accept request header. For example,
             * a client request for data formatted as application/xml will receive a 406 response
             * if the API is only willing to format data as application/json.
             *
             * If the response could be unacceptable, a user agent SHOULD temporarily stop receipt
             * of more data and query the user for a decision on further actions.
             */
            case 'Not Acceptable':
                $code = 406;
                break;

            /**
             * The 412 error response indicates that the client specified one or more preconditions
             * in its request headers, effectively telling the REST API to carry out its request
             * only if certain conditions were met. A 412 response indicates that those conditions
             * were not met, so instead of carrying out the request, the API sends this status code.
             */
            case 'Precondition Failed':
                $code = 412;
                break;

            /**
             * The 415 error response indicates that the API is not able to process the client’s
             * supplied media type, as indicated by the Content-Type request header. For example,
             * a client request including data formatted as application/xml will receive a 415
             * response if the API is only willing to process data formatted as application/json.
             *
             * For example, the client uploads an image as image/svg+xml, but the server requires
             * that images use a different format.
             */
            case 'Unsupported Media Type':
                $code = 415;
                break;

            /**
             * 500 is the generic REST API error response. Most web frameworks automatically respond
             * with this response status code whenever they execute some request handler code that
             * raises an exception.
             *
             * A 500 error is never the client’s fault, and therefore, it is reasonable for the client
             * to retry the same request that triggered this response and hope to get a different response.
             *
             * API response is the generic error message, given when an unexpected condition was
             * encountered and no more specific message is suitable.
             */
            case 'Internal Server Error':
                $code = 500;
                break;

            /**
             * The server either does not recognize the request method, or it lacks the ability to
             * fulfill the request. Usually, this implies future availability (e.g., a new feature
             * of a web-service API).
             */
            case 'Not Implemented':
                $code = 501;
                break;

            default:
                $code = 200;
        }

        return http_response_code($code);
    }
}