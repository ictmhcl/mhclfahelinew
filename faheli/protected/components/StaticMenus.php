<?php

/*
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from ... in writing.
 */

/**
 * Description of StaticMenus
 *
 * @author nazim
 */
class StaticMenus {

    public static $menus = [
        ['label' => 'New Registration', 'url' => ['create']],
        ['label' => 'Incomplete Registrations', 'url' => ['incomplete']],
        ['label' => 'Registrations Pending Verification', 'url' => ['verify']],
        ['label' => 'Pending Payment Forms', 'url' => ['payment']],
    ];
    public static $memberMenus = [
        ['label' => 'Members List', 'url' => ['list']],
        ['label' => 'Search Member', 'url' => ['search']],
        ['label' => 'Member Statement', 'url' => ['search']],
    ];
    public static $userMenus = [
        ['label' => 'Create Users', 'url' => ['create']],
        ['label' => 'Change my password and Contact Info', 'url' => ['selfUpdate']],
        ['label' => 'User Collections', 'url' => ['collections']],
        ['label' => 'Manage Users', 'url' => ['list']],
    ];
    
    public static $flightMenus = [
        ['label' => 'Create Users', 'url' => ['create']],
        ['label' => 'Manage Users', 'url' => ['list']],
    ];
    
    

    public static function hajjMenus() {
        $nextBatch = Helpers::nextBatch();
        return [
            ($nextBatch)?
                ['label' => 'Next Hajj Batch', 'url' => ['/hajjs/view/'.$nextBatch]] :
                ['label' => 'Create Next Hajj Batch', 'url' => ['create']],
            ['label' => 'Hajjs', 'url' => ['list']],
        ];
    }

    //put your code here
}
