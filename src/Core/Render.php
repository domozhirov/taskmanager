<?php

namespace App\Core;

class Render
{
    /**
     * @param Request $request
     * @throws \SmartyException
     */
    public function htmlFormat(Request $request)
    {
        $render = new \Smarty();

        $render->setTemplateDir(APP_DIR . "/src/Views");
        $render->setCacheDir(APP_DIR . '/var/smarty/cache');
        $render->setCompileDir(APP_DIR . '/var/smarty/template_c');
        $render->setErrorReporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_USER_WARNING & ~E_USER_NOTICE & ~E_WARNING & ~E_USER_WARNING);

        $controller = strtolower($request->getController());
        $action     = strtolower($request->getAction());
        $error      = $request->getError();
        $tpls       = [];

        if ($error) {
            switch ($code = $error->getCode()) {
                case '404':
                case '500':
                case '503':
                    $tpls[] = "error/$code.tpl";
                    break;

                default:
                    $tpls[] = "error/default.tpl";
            }
        } else {
            $request->setStatus(200);

            $tpls = [
                "$controller/$action.tpl",
                "$controller/default.tpl",
            ];
        }

        foreach ($tpls as $tpl) {
            if ($render->templateExists($tpl)) {
                $render->display($tpl, static::getTemplateData($request));
                return;
            }
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    protected static function getTemplateData(Request $request): array
    {
        $action = $request->getActor();

        $data['request'] = $request;
        $data['action']  = $action;
        $data['content'] = $request->getData();
        $data['user']    = $request->getEngine()->getUser();
        $data['now']     = time();

        return $data;
    }

    public function jsonFormat(Request $request)
    {
        $request->setStatus(200, 'text/json');

        $response = [];

        if ($error = $request->getError()) {
            while ($error->getPrevious()) {
                $error = $error->getPrevious();
            }
            $response["error"] = [
                "code"      => $error->getCode(),
                "message"   => $message = $error->getMessage(),
                "exception" => get_class($error),
            ];

            if ($out = $request->getOut()) {
                $response["error"]["stdout"] = $out;
            }
        } else {
            $response["result"] = $request->getData();
        }

        echo json_encode($response,
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_BIGINT_AS_STRING);

        if (json_last_error()) {
            $response["error"] = [
                "error" => [
                    "message"   => json_last_error_msg(),
                    "code"      => 500,
                    "exception" => "ErrorException",
                ],
            ];
            unset($response["result"]);
            echo json_encode($response,
                JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_BIGINT_AS_STRING);
        }
    }
}
