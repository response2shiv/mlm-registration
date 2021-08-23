<?php

namespace App\Services;

use App\Facades\BinaryPlanManager;
use App\Models\BinaryPlanNode;
use App\Models\User;
use App\Models\ProductType;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Class BinaryPlanTreeService
 * @package App\Services
 */
class BinaryPlanService
{
    const RIGHT_LEG_KEY = '2';
    const LEFT_LEG_KEY = '1';

    /**
     * @param User $user
     * @return BinaryPlanNode
     */
    public function createNode(User $user)
    {
        $sponsor = User::where('distid', $user->sponsorid)->first();

        $node = new BinaryPlanNode();
        $node->user_id = $user->id;
        $node->sponsor_id = $sponsor ? $sponsor->id : null;
        $node->enrolled_at = DateTime::createFromFormat(
            "!Y-m-d",
            $user->created_date,
            new DateTimeZone( 'UTC' )
        );

        return $node;
    }

    /**
     * @param $rootNode
     * @param $newNode
     * @throws Exception
     */
    public function addLeftLeg($rootNode, $newNode)
    {
        $isNodeExists = BinaryPlanNode::where('user_id', $newNode->user_id)->count();

        if ($isNodeExists > 0) {
            throw new Exception('Node with the target user is already exists.');
        }
           // Set the default direction of next node insertions
        $newNode->setLeftDirection();
        $newNode->depth = $rootNode->depth + 1;

        // TODO: Add enrolled_at value

        $rootNode->appendNode($newNode);
    }

    /**
     * @param $rootNode
     * @param $newNode
     * @throws Exception
     */
    public function addRightLeg($rootNode, $newNode)
    {
        $isNodeExists = BinaryPlanNode::where('user_id', $newNode->user_id)->count();

        if ($isNodeExists > 0) {
            throw new Exception('Node with the target user is already exists.');
        }

        // Set the default direction of next node insertions
        $newNode->setRightDirection();
        $newNode->depth = $rootNode->depth + 1;

        // TODO: Add enrolled_at value

        $rootNode->appendNode($newNode);
    }

    /**
     * Multiple insert of nodes with specific direction.
     *
     * @param $rootNode
     * @param array $nodes
     * @param $direction
     * @throws Exception
     */
    public function placeLegs($rootNode, array $nodes, $direction)
    {
        foreach ($nodes as $node) {
            switch ($direction) {
                case BinaryPlanManager::DIRECTION_LEFT:
                    // this is time consuming and should be optimized
                    $this->addLeftLeg($this->getLastLeftNode($rootNode), $node);
                    break;
                case BinaryPlanManager::DIRECTION_RIGHT:
                    // this is time consuming and should be optimized
                    $this->addRightLeg($this->getLastRightNode($rootNode), $node);
                    break;
            }
        }
    }

    /**
     * Multiple insert of nodes with auto-direction.
     *
     * @param $rootNode
     * @param array $nodes
     * @param $defaultDirection
     * @throws Exception
     */
    public function autoPlaceLegs($rootNode, array $nodes, $defaultDirection): void
    {
        $direction = $defaultDirection;

        foreach ($nodes as $node) {
            switch ($direction) {
                case BinaryPlanNode::DIRECTION_LEFT:
                    // this is time consuming and should be optimized
                    $this->addLeftLeg($this->getLastLeftNode($rootNode), $node);
                    break;
                case BinaryPlanNode::DIRECTION_RIGHT:
                    // this is time consuming and should be optimized
                    $this->addRightLeg($this->getLastRightNode($rootNode), $node);
                    break;
            }

            // toggle direction
            $direction = $direction === BinaryPlanNode::DIRECTION_LEFT
                ? BinaryPlanNode::DIRECTION_RIGHT
                : BinaryPlanNode::DIRECTION_LEFT;
        }
    }

    /**
     * @param $node
     * @return mixed
     */
    public function getLastLeftNode($node)
    {
        $lastLeg = $node;
        while ($processedNode = $this->getLeftLeg($lastLeg)) {
            $lastLeg = $processedNode;
        }

        return $lastLeg;

    }

    /**
     * @param $node
     * @return mixed
     */
    public function getLastRightNode($node)
    {
        $lastLeg = $node;
        while ($processedNode = $this->getRightLeg($lastLeg)) {
            $lastLeg = $processedNode;
        }

        return $lastLeg;
    }

    /**
     * @param $rootNode
     * @return mixed
     */
    public function getLastPlacedNode($rootNode)
    {
        if ($this->isLeaf($rootNode)) {
            $lastNode = $rootNode;
        } else {
            $lastNode  = $rootNode->direction === BinaryPlanNode::DIRECTION_LEFT
                ? $this->getLastLeftNode($rootNode)
                : $this->getLastRightNode($rootNode);
        }

        return $lastNode;
    }

    /**
     * @param $node
     * @return bool
     */
    private function isLeaf($node)
    {
        return BinaryPlanNode::where('parent_id', $node->id)->count() === 0;
    }

    /**
     * @param $node
     * @return mixed
     */
    public function getRightLeg($node)
    {
        return BinaryPlanNode::where('parent_id', $node->id)
            ->where('direction', BinaryPlanNode::DIRECTION_RIGHT)
            ->first();
    }

    /**
     * @param $node
     * @return mixed
     */
    public function getLeftLeg($node)
    {
        return BinaryPlanNode::where('parent_id', $node->id)
            ->where('direction', BinaryPlanNode::DIRECTION_LEFT)
            ->first();
    }

    /**
     * @param $users
     * @return array
     */
    public function createNodesByUsers($users)
    {
        $nodes = [];

        foreach ($users as $user) {
            $nodes[] = $this->createNode($user);
        }

        return $nodes;
    }

    /**
     * @param $rootNode
     * @param $date
     * @return mixed
     */
    public function getNodeTotal($rootNode, $date)
    {
        $result = DB::table('binary_plan')
            ->leftJoin('orders', 'binary_plan.user_id', '=', 'orders.userid')
            ->leftJoin('orderItem', 'orders.id', '=', 'orderItem.orderid')
            ->leftJoin('products', 'products.id', '=', 'orderItem.productid')
            ->select(DB::raw("COALESCE(SUM(\"orderItem\".cv), 0) as sum_orders"))
            ->where('binary_plan._lft', '>=', $rootNode->_lft)
            ->where('binary_plan._rgt', '<=', $rootNode->_rgt)
            ->whereIn('products.producttype', [
                ProductType::TYPE_ENROLLMENT,
                ProductType::TYPE_UPGRADE
            ])
            ->whereDate('orders.created_dt', '>=', $date)
            ->value('sum_orders');

        return $result;
    }

    /**
     * @param $tsaNumber
     * @return |null
     */
    public function getNodeByAgentTsa($tsaNumber)
    {
        $node = null;

        $recordId = DB::table('binary_plan')
            ->select('binary_plan.id')
            ->join('users', 'binary_plan.user_id', '=', 'users.id')
            ->where('users.distid', $tsaNumber)
            ->pluck('id')
            ->first();

        if ($recordId) {
            $node = BinaryPlanNode::where('id', $recordId)->first();
        }

        return $node;
    }
}
