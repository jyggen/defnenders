<?php
namespace Defnenders\Controller;

use Defnenders\Core\Application;
use Defnenders\Repository\ArmorTypeRepository;
use Defnenders\Repository\ClassRepository;
use Defnenders\Repository\RoleRepository;
use Defnenders\Repository\TokenRepository;

class WebController
{
    protected $armorTypes;
    protected $classes;
    protected $roles;
    protected $tokens;

    public function __construct(ArmorTypeRepository $armorTypes, ClassRepository $classes, RoleRepository $roles, TokenRepository $tokens)
    {
        $this->armorTypes = $armorTypes;
        $this->classes    = $classes;
        $this->roles      = $roles;
        $this->tokens     = $tokens;
    }

    public function indexAction(Application $app)
    {
        $registerNotice = false;
        $app['me_info'] = ['nickname' => '', 'specialization_id' => null];

        if ($app['is_logged_in'] === true) {
            $select = $app['pdo.query']->newSelect();
            $query  = $select->cols(['*'])->from('members')->where('id = :member_id')->limit(1)->bindValue('member_id', $app['user_info']['id']);
            $me     = $app['pdo']->fetchOne($query->__toString(), $query->getBindValues());

            if ($me === false) {
                $registerNotice = true;
            } else {
                $app['me_info'] = ['nickname' => $me['nickname'], 'specialization_id' => $me['specialization_id']];
            }
        }

        $allData = ['registerNotice' => $registerNotice];
        $select  = $app['pdo.query']->newSelect();
        $query   = $select->cols(['specializations.id, specializations.name as name, classes.name as class'])->from('specializations')->join('left', 'classes', 'classes.id = specializations.class_id')->orderBy(['classes.name', 'specializations.name']);
        $specs   = $app['pdo']->fetchAssoc($query->__toString(), $query->getBindValues());
        $select  = $app['pdo.query']->newSelect();
        $query   = $select->cols(['members.id', 'members.name', 'members.nickname', 'specializations.name as spec', 'classes.name as class', 'classes.color as color', 'roles.name as role', 'tokens.name as token', 'armor_types.name as armor'])
                          ->from('members')
                          ->join('left', 'specializations', 'specializations.id = members.specialization_id')
                          ->join('left', 'classes', 'classes.id = specializations.class_id')
                          ->join('left', 'roles', 'roles.id = specializations.role_id')
                          ->join('left', 'tokens', 'tokens.id = classes.token_id')
                          ->join('left', 'armor_types', 'armor_types.id = classes.armor_type_id')
                          ->orderBy(['members.name']);
        $members = $app['pdo']->fetchAssoc($query->__toString(), $query->getBindValues());

        foreach ($this->classes->all() as $class) {
            $allData['classes'][$class['name']] = [];
        }

        foreach ($this->roles->all() as $role) {
            $allData['roles'][$role['name']] = [];
        }

        foreach ($this->tokens->all() as $token) {
            $allData['tokens'][$token['name']] = [];
        }

        foreach ($this->armorTypes->all() as $armor) {
            $allData['armors'][$armor['name']] = [];
        }

        foreach ($specs as $spec) {
            $allData['specializations'][$spec['class']][] = $spec;
        }

        foreach ($members as $member) {
            $allData['classes'][$member['class']][] = $member;
            $allData['roles'][$member['role']][]    = $member;
            $allData['tokens'][$member['token']][]  = $member;
            $allData['armors'][$member['armor']][]  = $member;
        }

        return $app['twig']->render('index.twig', $allData);
    }
}
