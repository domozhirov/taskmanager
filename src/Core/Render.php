<?php

namespace App\Core;

class Render
{
    /**
     * @param Request $request
     * @throws \SmartyException
     */
    public function htmlFormat(Request $request) {
        $render = new \Smarty();

        $render->setTemplateDir(APP_DIR . "/src/Views");
        $render->setCacheDir(APP_DIR. '/var/smarty');

        $controller = $request->getController();
        $action = $request->getAction();

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
}
