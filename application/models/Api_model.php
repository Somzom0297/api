<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_account($id)
    {
        $sql = "SELECT * FROM sys_account WHERE sa_id = $id";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function chk_login_db($data)
    {
        return $data;
    }

    public function get_menu($sess)
    {
        $sql = "SELECT
            sys_permission_detail.spd_id,
            sys_permission_group.spg_id,
            sys_main_menu.smm_name,
            sys_main_menu.smm_order,
            sys_main_menu.smm_icon, 
            sys_sub_menu.ssm_name,
            sys_sub_menu.ssm_controller,
            sys_sub_menu.ssm_order_no,
            sys_permission_detail.ssm_id,
            sys_permission_detail.spd_status_flg
        FROM
            sys_permission_detail
        JOIN
            sys_permission_group ON sys_permission_detail.spg_id = sys_permission_group.spg_id
        JOIN
            sys_sub_menu ON sys_permission_detail.ssm_id = sys_sub_menu.ssm_id
        JOIN
            sys_main_menu ON sys_sub_menu.smm_id = sys_main_menu.smm_id
        WHERE
            sys_permission_group.spg_id = '$sess' AND spd_status_flg = 1
        ORDER BY
            sys_main_menu.smm_order ASC";

        $query = $this->db->query($sql, array($sess));
        $data = $query->result();

        return $this->organizeMenuData($data);
    }

    private function organizeMenuData($menuData)
    {
        $sidebarMenu = [];

        foreach ($menuData as $menuItem) {
            $mainMenuName = $menuItem->smm_name;
            $mainMenuIcon = $menuItem->smm_icon;
            $subMenuName = $menuItem->ssm_name;
            $subMenuController = $menuItem->ssm_controller;

            if (!isset($sidebarMenu[$mainMenuName])) {
                $sidebarMenu[$mainMenuName] = [
                    'icon' => $mainMenuIcon,
                    'submenus' => [],
                ];
            }

            if (!in_array(['name' => $subMenuName, 'controller' => $subMenuController], $sidebarMenu[$mainMenuName]['submenus'])) {
                $sidebarMenu[$mainMenuName]['submenus'][] = [
                    'name' => $subMenuName,
                    'controller' => $subMenuController,
                ];
            }
        }

        return $sidebarMenu;
    }
}
