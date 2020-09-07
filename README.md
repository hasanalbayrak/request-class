# request-class
Provides basic level HTTP request control and implementation for PHP.

Smarty template engine is also included. After adding the Smarty template engine to the system, when it is defined as $smarty, you can run the smarty commands with the commands below.

## Methods
Call the class;
```
use Hasan\App\Request;

$Request = new Request();
```

Create a session and define something in the session.
```
$Request->setSession("name", "value");

// example;
$Request->setSession("language", "tr");
```

Read the session.
```
$Request->getSession("language");
```

Encode as JSON
```
$Request->json(array(), JSON_OPTIONS);

// example;
$Request->json(["route" => '/'], true);
```

Call POST, GET, FILE, SERVER and SESSION.
```
// Default second args. POST
$Request->request("PostInput");

// GET
$Request->request("pid", "GET");

// FILES
$Request->request("upload", "FILES");

// SERVER
$Request->request("HTTP_HOST", "SERVER");
```

## Methods for Smarty
Specify the page title and call the page title {$ page_title}.
```
$Request->page_title("Page Title");
```

It shows the TPL file and shows the current template name with {$templatefile}.
```
$Request->display("Template File Without Tpl extension");
```

Provide all with one command at the end of the page. To do this, call the view function.
```
$Request->view(
  'template_file_without_tpl_extension',
  'page_title',
  // extra assigns smarty variables
  [
    'smartyvariable' => 'phpvariable',
    'smartyvariable2' => 'phpvariable2',
    'smartyvariable3' => 'phpvariable3',
  ]
);
```

Define globally with assign.
```
$Request->assign('smartyvariable', 'phpvariable');
```

If you want to use it as Restfull API, you can provide it with http response messages.
```
$Request->status("OK"); // Status Code 200
$Request->status("Not Found"); // Status Code 404
```

All http messages provided are as follows;
- OK - 200
- Created - 201
- Accepted - 202
- No Content - 204
- Moved Permanently - 301
- Found - 302
- See Other - 303
- Not Modified - 304
- Temporary Redirect - 307
- Bad Request - 400
- Unauthorized - 401
- Forbidden - 403
- Not Found - 404
- Method Not Allowed - 405
- Not Acceptable - 406
- Precondition Failed - 412
- Unsupported Media Type - 415
- Internal Server Error - 500
- Not Implemented - 501
