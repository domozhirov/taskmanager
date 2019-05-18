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

        $controller = $request->getController();
        $action     = $request->getAction();

        $request->setStatus(200);

        $tpls = [
            strtolower($controller) . '/' . strtolower($action) . '.tpl',
            strtolower($controller) . '/default.tpl',
            'default.tpl',
        ];

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
