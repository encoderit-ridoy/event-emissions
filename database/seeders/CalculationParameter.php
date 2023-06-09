<?php

namespace Database\Seeders;

use App\Models\MeetingRoomManagement;
use App\Models\OnlineMeetingManagement;
use App\Models\OtherActivitiesManagement;
use App\Models\TransportationManagement;
use Illuminate\Database\Seeder;

class CalculationParameter extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $meetingrooms = [
            ['meeting_room' => '1F-カンファレンスA', 'electicity_parameter' => '0.630', 'unit' => 'hr'],
            ['meeting_room' => '1F-カンファレンスB', 'electicity_parameter' => '0.507', 'unit' => 'hr'],
            ['meeting_room' => '1F-カンファレンスC', 'electicity_parameter' => '0.760', 'unit' => 'hr'],
            ['meeting_room' => '1F-カンファレンスD', 'electicity_parameter' => '0.577', 'unit' => 'hr'],
            ['meeting_room' => '1F-ルームF', 'electicity_parameter' => '0.265', 'unit' => 'hr'],
            ['meeting_room' => '1F-ルームG', 'electicity_parameter' => '0.348', 'unit' => 'hr'],
            ['meeting_room' => '1F-ゲストルーム', 'electicity_parameter' => '0.141', 'unit' => 'hr'],
            ['meeting_room' => '２F-ホールA', 'electicity_parameter' => '2.150', 'unit' => 'hr'],
            ['meeting_room' => '２F-ホールB', 'electicity_parameter' => '2.150', 'unit' => 'hr'],
            ['meeting_room' => '２F-カンファレンスE', 'electicity_parameter' => '0.459', 'unit' => 'hr'],
            ['meeting_room' => '２F-ルームH', 'electicity_parameter' => '0.230', 'unit' => 'hr'],
            ['meeting_room' => '２F-ルームI', 'electicity_parameter' => '0.224', 'unit' => 'hr'],
            ['meeting_room' => '２F-ルームJ', 'electicity_parameter' => '0.265', 'unit' => 'hr'],
        ];

        foreach ($meetingrooms as $room) {
            MeetingRoomManagement::create($room);
        }


        $transportaions = [
            ['transporation_way' => '電車', 'parameter' => '0.00185', 'unit' => '人・円'],
            ['transporation_way' => 'バス', 'parameter' => '0.00471', 'unit' => '人・円'],
            ['transporation_way' => 'タクシー・ハイヤー', 'parameter' => '0.00331', 'unit' => '人・円'],
            ['transporation_way' => '旅客航空機（国内）', 'parameter' => '0.00525', 'unit' => '人・円'],
            ['transporation_way' => '旅客航空機（国際）', 'parameter' => '0.00710', 'unit' => '人・円'],
            ['transporation_way' => '旅客船舶', 'parameter' => '0.05019', 'unit' => '人・円'],
            ['transporation_way' => '自家用乗用車', 'parameter' => '0.19800', 'unit' => '台・km'],
        ];

        foreach ($transportaions as $row) {
            TransportationManagement::create($row);
        }

        $onlinemeeting = [
            ['item' => '参加者、視聴用PC台数', 'parameter' => '0.020', 'unit' => 'hr'],
            ['item' => '配信用機材一式', 'parameter' => '0.439', 'unit' => 'hr'],
        ];

        foreach ($onlinemeeting as $row) {
            OnlineMeetingManagement::create($row);
        }

        $otheractivities = [
            ['item' => '宿泊', 'parameter' => '31.532', 'unit' => '泊・人'],
            ['item' => '印刷物', 'parameter' => '0.011', 'unit' => '枚'],
            ['item' => '筆記具・文具　', 'parameter' => '0.160', 'unit' => '本'],
            ['item' => '500ml ペットボトル飲料　(リサイクル含)', 'parameter' => '0.122', 'unit' => '本'],
            ['item' => '350mlアルミ缶飲料（リサイクル含）', 'parameter' => '0.103', 'unit' => '本'],
            ['item' => '633mlビール瓶（リサイクル含）', 'parameter' => '0.085', 'unit' => '本'],
            ['item' => '633mlビール瓶（リサイクル含）', 'parameter' => '0.085', 'unit' => '本'],
            ['item' => '食事の提供', 'parameter' => '4.210', 'unit' => '千円'],
            ['item' => '一般廃棄物', 'parameter' => '2.690', 'unit' => 'kg'],
        ];

        foreach ($otheractivities as $row) {
            OtherActivitiesManagement::create($row);
        }
    }
}
