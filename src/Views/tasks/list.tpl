{include file="layout/header.tpl"}

<div class="container mt-2">
    <div class="row justify-content-md-center">
        <div class="col-sm-6">
            <ul class="nav nav-pills mb-4">
                <li class="nav-item dropdown ml-auto">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                        {if $user}
                            {$user->getName()}
                        {else}
                            Sign in
                        {/if}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        {if $user}
                            <a href="#"class="dropdown-item" id="logout">Logout</a>
                        {else}
                            <form class="px-4 py-3" id="login" style="width: 280px">
                                <div class="form-group">
                                    <label for="login">Login</label>
                                    <input type="text" class="form-control" name="login" id="login" placeholder="Your login">
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                                </div>
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="remember" id="remember" value="1">
                                        <label class="form-check-label" for="remember">
                                            Remember me
                                        </label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Sign in</button>
                            </form>
                        {/if}
                    </div>
                </li>
            </ul>
            <h1>Task manager</h1>

            <form id="form" action="/task/add.json" method="post">
                {if $user && $user->getAccess() == 10}
                    <input name="name" type="hidden" value="{$user->getName()}">
                    <input name="email" type="hidden" value="{$user->getEmail()}">
                {else}
                    <div class="form-group">
                        <input id="main-input" name="name" type="text" class="form-control" placeholder="Your name" required>
                    </div>

                    <div class="form-group">
                        <input id="main-input" name="email" type="email" class="form-control" placeholder="Email" required>
                    </div>
                {/if}

                <div class="form-group">
                    <input id="main-input" name="text" type="text" class="form-control" placeholder="Write up something" required>
                </div>

                <div class="row flex-nowrap">
                    <div class="col">
                        <button type="submit" class="btn btn-success text-nowrap" id="button" disabled="">Add A Task</button>
                    </div>
                    {if $content.total > 1}
                        <div class="col flex-fill"></div>
                        <div class="col d-flex align-items-center">
                            {if $smarty.get.param.sort_by}
                                <a href="/" class="mr-2">Reset</a>
                            {/if}
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Sort By
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                    {foreach from=['name', 'email', 'status'] item=$e}
                                        <div href="#" class="dropdown-item">
                                            <div class="item">
                                                {$e}
                                                <a href="/?param[sort_by]={$e} asc">↑</a>
                                                <a href="/?param[sort_by]={$e} desc">↓</a>
                                            </div>
                                        </div>
                                    {/foreach}
                                </div>
                            </div>
                        </div>
                    {/if}
                </div>

            </form>


            <div class="my-4" id="tasks">
                {foreach from=$content.tasks item=e}
                    <div class="card mb-3{if $e.status} border-success{/if}">
                        <div class="card-header border-bottom d-flex align-items-center">
                            <strong class="mr-2 mw-25 text-ellipsis" style="max-width: 35%;">{$e.name}</strong>
                            <small>{$e.email}</small>
                            {if $user}
                                <div class="controls ml-auto d-flex">
                                    <div class="item">
                                    <a href="#" class="task-editor d-block mr-2" data-id="{$e.id}" style="width: 15px;">
                                        <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="edit" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-inline--fa fa-edit fa-w-18 fa-2x"><path fill="currentColor" d="M402.3 344.9l32-32c5-5 13.7-1.5 13.7 5.7V464c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V112c0-26.5 21.5-48 48-48h273.5c7.1 0 10.7 8.6 5.7 13.7l-32 32c-1.5 1.5-3.5 2.3-5.7 2.3H48v352h352V350.5c0-2.1.8-4.1 2.3-5.6zm156.6-201.8L296.3 405.7l-90.4 10c-26.2 2.9-48.5-19.2-45.6-45.6l10-90.4L432.9 17.1c22.9-22.9 59.9-22.9 82.7 0l43.2 43.2c22.9 22.9 22.9 60 .1 82.8zM460.1 174L402 115.9 216.2 301.8l-7.3 65.3 65.3-7.3L460.1 174zm64.8-79.7l-43.2-43.2c-4.1-4.1-10.8-4.1-14.8 0L436 82l58.1 58.1 30.9-30.9c4-4.2 4-10.8-.1-14.9z" class=""></path></svg>
                                    </a>
                                    </div>
                                    <div class="item">
                                        <input data-id="{$e.id}" type="checkbox" name="status" style="cursor: pointer;" class="ml-auto task-change-status"{if $e.status} checked{/if} value="{$e.status}">
                                    </div>
                                </div>
                            {/if}
                        </div>
                        <div class="card-body">
                            <p class="card-text text-ellipsis">{$e.text}</p>
                        </div>
                    </div>
                {/foreach}
            </div>

            {if $content.tasks}
                {assign var=sort_by value=$smarty.get.param.sort_by}
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        {assign var=prev value=$content.page-1}
                        <li class="page-item{if $prev<0} disabled{/if}"><a class="page-link" href="{if $prev}/tasks/list/?param[p]={$prev}{if $sort_by}&param[sort_by]={$sort_by}{/if}{else}/{/if}{if $sort_by}?param[sort_by]={$sort_by}{/if}">Previous</a></li>

                        {if ($content.page > 2)}
                            {assign var=start value=$content.page-2}
                        {/if}
                        {if $content.page > 2}
                            <li class="page-item"><a class="page-link" href="/{if $sort_by}?param[sort_by]={$sort_by}{/if}">...</a></li>
                        {/if}
                        {section name=pagination loop=$content.pages step=1 start=$start max=5}
                            {assign var=index value=$smarty.section.pagination.index}
                            <li class="page-item{if $index===$content.page} active{/if}"><a class="page-link" href="{if $index}/tasks/list/?param[p]={$index}{if $sort_by}&param[sort_by]={$sort_by}{/if}{else}/{if $sort_by}?param[sort_by]={$sort_by}{/if}{/if}">{$index+1}</a></li>
                        {/section}
                        {if ($content.page + 3 < $content.pages)}
                            <li class="page-item"><a class="page-link" href="/tasks/list/?param[p]={$content.pages-1}{if $sort_by}&param[sort_by]={$sort_by}{/if}">...</a></li>
                        {/if}
                        {assign var=next value=$content.page+1}
                        <li class="page-item{if $next>=$content.pages} disabled{/if}"><a class="page-link" href="/tasks/list/?param[p]={$next}{if $sort_by}&param[sort_by]={$sort_by}{/if}">Next</a></li>
                    </ul>
                </nav>
            {/if}

        </div>
    </div>

    {if $user && $user->getAccess() == 10}
        <div class="modal fade" tabindex="-1" role="dialog" id="editModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="/tasks/changeText.json" id="edit-form">
                            <input type="hidden" name="id" value="">
                            <textarea class="form-control" name="text" rows="6"></textarea>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="edit-button" data-dismiss="modal" disabled="">
                            Save changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    {/if}
</div>

{include file="layout/bottom.tpl"}


