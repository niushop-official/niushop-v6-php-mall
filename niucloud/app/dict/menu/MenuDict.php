<?php
// +----------------------------------------------------------------------
// | Niucloud-admin 企业快速开发的多应用管理平台
// +----------------------------------------------------------------------
// | 官方网址：https://www.niucloud.com
// +----------------------------------------------------------------------
// | niucloud团队 版权所有 开源版本可自由商用
// +----------------------------------------------------------------------
// | Author: Niucloud Team
// +----------------------------------------------------------------------

namespace app\dict\menu;


/**
 * 菜单类
 * Class MenuDict
 * @package app\dict\sys
 */
class MenuDict
{

    public const ADDON_CHILD_MENU_DICT_SYSTEM_TOOL = 'system_tool';
    public const ADDON_CHILD_MENU_DICT_MARKING_TOOL = 'marketing_tool';
    public const ADDON_CHILD_MENU_DICT_MARKING_ACTIVE = 'marketing_active';
    public const ADDON_CHILD_MENU_DICT_ADDON_TOOL = 'addon_tool';

    /**
     * 站点应用管理特殊子菜单
     * @return array
     */
    public static function getAddonChildMenu()
    {
        //注意  sort  倒序排序使用
        return [
            self::ADDON_CHILD_MENU_DICT_SYSTEM_TOOL => [
                'key' => self::ADDON_CHILD_MENU_DICT_SYSTEM_TOOL,
                'name' => get_lang('dict_addon_menu.system_tool'),
                'short_name' => get_lang('dict_addon_menu.system_tool_short'),
                'sort' => 97
            ],
            self::ADDON_CHILD_MENU_DICT_MARKING_TOOL => [
                'key' => self::ADDON_CHILD_MENU_DICT_MARKING_TOOL,
                'name' => get_lang('dict_addon_menu.marking_tool'),
                'short_name' => get_lang('dict_site.marking_tool_short'),
                'sort' => 99
            ],
            self::ADDON_CHILD_MENU_DICT_MARKING_ACTIVE => [
                'key' => self::ADDON_CHILD_MENU_DICT_MARKING_ACTIVE,
                'name' => get_lang('dict_addon_menu.marking_active'),
                'short_name' => get_lang('dict_addon_menu.marking_active_short'),
                'sort' => 100
            ],
            self::ADDON_CHILD_MENU_DICT_ADDON_TOOL => [
                'key' => self::ADDON_CHILD_MENU_DICT_ADDON_TOOL,
                'name' => get_lang('dict_addon_menu.addon_tool'),
                'short_name' => get_lang('dict_addon_menu.addon_tool_short'),
                'sort' => 98
            ],
        ];
    }
}