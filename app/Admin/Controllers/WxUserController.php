<?php

namespace App\Admin\Controllers;

use App\WeiXin\P_wx_users;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class WxUserController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\WeiXin\P_wx_users';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new P_wx_users);

        $grid->column('uid', __('Uid'));
        $grid->column('openid', __('Openid'));
        $grid->column('nickname', __('Nickname'));
        $grid->column('sex', __('Sex'));
        $grid->column('headimgurl', __('Headimgurl'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('subscribe_time', __('Subscribe time'));
        $grid->column('created_at', __('Created at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(P_wx_users::findOrFail($id));

        $show->field('uid', __('Uid'));
        $show->field('openid', __('Openid'));
        $show->field('nickname', __('Nickname'));
        $show->field('sex', __('Sex'));
        $show->field('headimgurl', __('Headimgurl'));
        $show->field('updated_at', __('Updated at'));
        $show->field('subscribe_time', __('Subscribe time'));
        $show->field('created_at', __('Created at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new P_wx_users);

        $form->text('openid', __('Openid'));
        $form->text('nickname', __('Nickname'));
        $form->switch('sex', __('Sex'));
        $form->text('headimgurl', __('Headimgurl'));
        $form->number('subscribe_time', __('Subscribe time'));

        return $form;
    }
}
