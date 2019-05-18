{include file="layout/header.tpl"}

<ul>
{foreach from=$content.tasks item=e}
    <li>{$e.id} | {$e.name} | {$e.email} | {$e.text}</li>
{/foreach}
</ul>

{include file="layout/bottom.tpl"}
