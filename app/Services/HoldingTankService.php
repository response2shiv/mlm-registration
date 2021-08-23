<?php

namespace App\Services;

use App\Facades\BinaryPlanManager;
use App\Facades\HoldingTank;
use App\Models\BinaryPlanNode;
use App\Models\User;
use App\Models\Orders;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Log;

/**
 * Class HoldingTankService
 * @package App\Services
 */
class HoldingTankService {

    /**
     * @param User $user
     * @return mixed
     */
    public function getRootBinaryNode(User $user) {
        return BinaryPlanNode::where('user_id', $user->id)->first();
    }

    /**
     * Place agents to the binary tree viewer.
     *
     * @param $targetNode
     * @param $distributors
     * @param $direction
     * @throws \Exception
     */
    public function placeAgentsToBinaryViewer($targetNode, $distributors, $direction): void {
        $isSinglePlacement = $distributors->count() === 1;

        if (Auth::check()) {
            $rootNode = HoldingTank::getRootBinaryNode(Auth::user());
        } else {
            $rootNode = $targetNode;
        }

        // update orders create dates to NOW()
        foreach ($distributors as $distributor) {
            $this->updateOrdersCreateDate($distributor);
        }

        switch ($direction) {
            case BinaryPlanManager::DIRECTION_RIGHT:
                if ($isSinglePlacement) {
                    BinaryPlanManager::addRightLeg(
                        BinaryPlanManager::getLastRightNode($targetNode), BinaryPlanManager::createNode($distributors->first())
                    );
                } else {
                    $newNodes = BinaryPlanManager::createNodesByUsers($distributors);
                    BinaryPlanManager::placeLegs($rootNode, $newNodes, $direction);
                }
                break;
            case BinaryPlanManager::DIRECTION_LEFT:
                if ($isSinglePlacement) {
                    BinaryPlanManager::addLeftLeg(
                        BinaryPlanManager::getLastLeftNode($targetNode), BinaryPlanManager::createNode($distributors->first())
                    );
                } else {
                    $newNodes = BinaryPlanManager::createNodesByUsers($distributors);
                    BinaryPlanManager::placeLegs($rootNode, $newNodes, $direction);
                }
                break;
            case BinaryPlanManager::DIRECTION_AUTO:
                $newNodes = BinaryPlanManager::createNodesByUsers($distributors);
                // generate new nodes and auto-place it to the structure
                BinaryPlanManager::autoPlaceLegs($rootNode, $newNodes, $targetNode->direction);
                break;
            default:
                throw new Exception('Invalid direction for the single node placement.');
        }
    }

    /**
     * Just update orders for the following node.
     *
     * @param $distributor
     */
    private function updateOrdersCreateDate($distributor)
    {
        $nodeOrders = Orders::where('userid', $distributor->id)->get();

        foreach ($nodeOrders as $order) {
            $order->created_dt = Carbon::now()->format('Y-m-d H:i:s');
            $order->created_date = Carbon::now()->format('Y-m-d');
            $order->created_time = Carbon::now()->format('H:i:s');
            $order->save();

            $orderItems = $order->orderItems;

            foreach ($orderItems as $orderItem) {
                $orderItem->created_dt = Carbon::now()->format('Y-m-d H:i:s');
                $orderItem->created_date = Carbon::now()->format('Y-m-d');
                $orderItem->created_time = Carbon::now()->format('H:i:s');
                $orderItem->save();
            }
        }
    }
}
