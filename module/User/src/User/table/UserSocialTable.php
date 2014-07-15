<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 13/07/14
 * Time: 1:15 PM
 */

namespace User\table;

use Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\AdapterAwareInterface,
    Zend\Db\TableGateway\AbstractTableGateway;
class UserSocialTable extends AbstractTableGateway implements AdapterAwareInterface
{
    protected $table = 'user_socials';

    /**
     * Set db adapter
     *
     * @param Adapter $adapter
     * @return AdapterAwareInterface
     */
    public function setDbAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function fetchBySocialAndSocialId($social = 0, $socialId = '')
    {
        $rowset = $this->select(array(
                'social'    => $social,
                'social_id' => $socialId
            )
        );
        $data = $rowset->current();
        if(false === $data) {
            return null;
        }
        return array(
            'userId'   => $data['user_id'],
            'socialId' => $data['social_id'],
            'picture'  => $data['picture']
        );
    }

    public function fetchByUserIdAndSocial($userId = 0, $social = 0)
    {
        $userId = (int) $userId;
        $social = (int) $social;
        $rowset = $this->select(array(
                'user_id' => $userId,
                'social' => $social
            )
        );
        $data = $rowset->current();
        if(false === $data) {
            return null;
        }
        return array(
            'userId'   => $data['user_id'],
            'socialId' => $data['social_id'],
            'picture'  => $data['picture']
        );
    }

    public function checkUserIdAndSocial($userId, $social, $socialId, $picture)
    {
        $data = $this->fetchByUserIdAndSocial($userId, $social);
        if(false != $data) {
            return $data;
        }
        $userId = (int) $userId;
        $social = (int) $social;
        $data = array(
            'user_id'   => $userId,
            'social'    => $social,
            'social_id' => $socialId,
            'picture'   => $picture
        );
        $numRowsAffected = $this->insert($data);
        if(!$numRowsAffected) {
            throw new \RuntimeException('Failed insertion in  "user_socials" table');
        }
        return $this->checkUserIdAndSocial($userId, $social, $socialId, $picture);
    }
} 