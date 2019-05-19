{include file="layout/header.tpl"}

<div class="container" style="margin-top:30px;">
    <div class="row justify-content-md-center">
        <div class="col-sm-6">
            <h1>Task manager</h1>

            <form id="form" action="/task/add" method="post">
                <div class="form-group">
                    <input id="main-input" name="name" type="text" class="form-control" placeholder="Your name" required>
                </div>

                <div class="form-group">
                    <input id="main-input" name="email" type="email" class="form-control" placeholder="Email" required>
                </div>

                <div class="form-group">
                    <input id="main-input" name="text" type="text" class="form-control" placeholder="Write up something" required>
                </div>

                <div class="row flex-nowrap">
                    <div class="col">
                        <button type="submit" class="btn btn-success text-nowrap" id="button" disabled="">Add A Task</button>
                    </div>
                    {if $content.total > 1}
                        <div class="col flex-fill"></div>
                        <div class="col">
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


            <ul class="list-group my-4" id="tasks">
                {foreach from=$content.tasks item=e}
                    <li class="list-group-item" data-toggle="modal" data-target="#editModal" data-task-id="{$e.id}">
                        <strong>{$e.name}</strong>  - <small>{$e.email}</small>
                        <hr class="mt-1 mb-1">
                        {$e.text}
                        <i class="fa fa-window-close" aria-hidden="true"></i>
                    </li>
                {/foreach}
            </ul>

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

    <div class="modal fade" id="editModal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" disabled="">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">Edit</h4>
                </div>

                <div class="modal-body">
                    <input type="text" class="form-control" id="edit-input">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="edit-button" data-dismiss="modal" disabled="">
                        Save changes
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

{include file="layout/bottom.tpl"}


