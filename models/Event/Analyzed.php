<?php
/**
 * Created by PhpStorm.
 * User: mkovac
 * Date: 9.1.2019
 * Time: 20:55
 */

namespace app\models\Event;
use app\models\AnalyzedSecurityEventsList;
use app\models\SecurityEvents;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;

/**
 * This is the model class for table "clustered_events".
 *
 * @property integer $id
 * @property string $time
 * @property string $raw
 * @property string $src_ip
 * @property string $dst_ip
 * @property string $country
 * @property string $city
 * @property string $src_city
 * @property double $src_latitude
 * @property double $latitude
 * @property double $src_longitude
 * @property double $longitude
 * @property string $code
 * @property string $src_code
 * @property integer $events_count
 * @property integer $iteration
 * @property integer $security_events_id
 * @property boolean $flag
 */

// TODO - urobit do viac vrstiev, hlbok, zoberie src ako dst a dst ako src a spravi zas dopyt (moznost zacyklenia)
class Analyzed extends \yii\db\ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return 'analyzed_events';
    }

    /**
     * @inheritdoc
     */

    public function rules()
    {
        return [
            [['time'], 'safe'],
            [['events_count', 'iteration', 'security_events_id'], 'integer'],
            [['flag'], 'boolean'],
            [['src_latitude', 'latitude', 'src_longitude', 'longitude'], 'double'],
            [['src_ip', 'dst_ip', 'country','city', 'src_city', 'code', 'src_code'], 'string', 'max' => 255],
        ];
    }

    /**
     * @param $params
     */
    public function Analyse($params){

        // get security events, both src and dst by IP
        $securityEventsSrc = self::getSecuritySrcIP($params[':id']);
        $securityEventsDst = self::getSecurityDstIP($params[':id']);

        // group by IPs
        $countsSRC = [];
        $groupedSrc = self::array_group_byCustom($countsSRC, $securityEventsSrc, "source_address", "destination_address");
        $countsDST = [];
        $groupedDst = self::array_group_byCustom($countsDST, $securityEventsDst, "source_address", "destination_address");

        // save security events to analyzed_security_events_list and change status of event to analyzed (true)
        $iteration = 0;
        self::saveAnalyzedSRC($groupedSrc,$countsSRC,$params,$iteration);
        self::saveAnalyzedDST($groupedDst,$countsDST,$params,$iteration);

        // to remove duplicity in analyse
        $result = array_merge($securityEventsDst, $securityEventsSrc);
        $resultEvents = (array_values(array_unique($result, SORT_REGULAR)));

        // save security to analyzed_security_events_list
        self::saveSecurityEvent($resultEvents, $params[':id'], $iteration);
    }

    /**
     * @param $params
     * @return array
     */
    private static function getSecuritySrcIP($params){
        try {
            return Yii::$app->db->createCommand(/** @lang text */
                "SELECT * FROM security_events n where n.destination_address = (SELECT source_address FROM security_events where id=:id AND source_address != '') OR n.source_address = (SELECT source_address FROM security_events where id=:id AND source_address != '')")
                ->bindValue(':id',$params)
                ->queryAll();
        }
        catch(Exception $e) {
            echo 'Message: ' .$e->getMessage();
        }
    }

    /**
     * @param $params
     * @return array
     */
    private static function getSecurityDstIP($params){
        try {
            return Yii::$app->db->createCommand(/** @lang text */
                "SELECT * FROM security_events n where n.destination_address = (SELECT destination_address FROM security_events where id=:id AND destination_address != '') OR n.source_address = (SELECT destination_address FROM security_events where id=:id AND destination_address != '') ORDER BY CASE WHEN id=:id THEN '1' ELSE id END ASC")
                ->bindValue(':id', $params)
                ->queryAll();
        } catch (Exception $e) {
            echo 'Message: ' .$e->getMessage();
        }
    }

    /**
     * @param $securityEvents
     * @param $id
     * @param $fieldVal2
     */

    private static function saveSecurityEvent($securityEvents, $id, $fieldVal2){
        if(is_array($securityEvents) && count($securityEvents) > 0){
            $max = sizeof($securityEvents);
            for ($i = 0; $i< $max;$i++){
                $analyzedSecurityEventsList = new AnalyzedSecurityEventsList;
                $analyzedSecurityEventsList->events_analyzed_iteration = $fieldVal2;
                $analyzedSecurityEventsList->analyzed_security_events_id = pg_escape_string($securityEvents[$i]["id"]);
                $analyzedSecurityEventsList->security_events_id = $id;
                $analyzedSecurityEventsList->save(true);
            }
        }

        if ($id != 0) {
            $model = SecurityEvents::findOne($id);
            $model->analyzed = true;
            $model->save(false);
        }

    }

    /**
     * @param $groupedSrc
     * @param $counts
     * @param $params
     * @param $fieldVal2
     */
    private static function saveAnalyzedSRC($groupedSrc, $counts, $params, &$fieldVal2){
        // need to know all events with the same analyse (iteration flag)
        $model = Analyzed::find()->orderBy('iteration DESC')->limit(1)->one();
        if (!empty($model->iteration))
            $fieldVal2 = $model->iteration;
        else {
            $model = new Analyzed;
            $model->iteration = 0;
            $model->save(false);
        }
        $fieldVal2++;

        date_default_timezone_set('Europe/Bratislava');
        foreach ($groupedSrc as &$value2) {
            $value3 = &$value2;
            foreach ($value3 as &$value) {
                $model = new Analyzed;
                $max = sizeof($value);
                for ($i = 0; $i < $max; $i++) {
                    $model->time = date("Y-m-d H:i:s") ?? "";
                    $model->src_ip = $value[$i]["source_address"] ?? "";
                    $model->dst_ip = $value[$i]["destination_address"] ?? "";
                    $model->code = $value[$i]["destination_code"] ?? "";
                    $model->country = $value[$i]["destination_country"] ?? "";
                    $model->city = $value[$i]["destination_city"] ?? "";
                    $model->latitude = $value[$i]["destination_geo_latitude"] ?? 0;
                    $model->longitude = $value[$i]["destination_geo_longitude"] ?? 0;
                    $model->src_latitude = $value[$i]["source_geo_latitude"] ?? 0;
                    $model->src_longitude = $value[$i]["source_geo_longitude"] ?? 0;
                    $model->src_city = $value[$i]["source_city"] ?? "";
                    $model->events_count = $counts[$value[$i]["source_address"]] ?? 0;
                    $model->security_events_id = $params[':id'] ?? "";
                    $model->iteration = $fieldVal2;
                    $model->flag = false;
                    $model->src_code = $value[$i]["source_code"] ?? "";
                }
                $model->save(true);
            }
        }
    }

    /**
     * @param $groupedDst
     * @param $counts
     * @param $params
     * @param $fieldVal2
     */

    private static function saveAnalyzedDST($groupedDst, $counts, $params, $fieldVal2){
        date_default_timezone_set('Europe/Bratislava');
        foreach ($groupedDst as &$value2) {
            $value3 = &$value2;
            foreach ($value3 as &$value) {
                $model = new Analyzed;
                $max = sizeof($value);
                for ($i = 0; $i < $max; $i++) {
                    $model->time = date("Y-m-d H:i:s") ?? "";
                    $model->src_ip = $value[$i]["destination_address"] ?? "";
                    $model->dst_ip = $value[$i]["source_address"] ?? "";
                    $model->code = $value[$i]["source_code"] ?? "";
                    $model->country = $value[$i]["source_country"] ?? "";
                    $model->city = $value[$i]["source_city"] ?? "";
                    $model->latitude = $value[$i]["source_geo_latitude"] ?? 0;
                    $model->longitude = $value[$i]["source_geo_longitude"] ?? 0;
                    $model->src_latitude = $value[$i]["destination_geo_latitude"] ?? 0;
                    $model->src_longitude = $value[$i]["destination_geo_longitude"] ?? 0;
                    $model->src_city = $value[$i]["destination_city"] ?? "";
                    $model->events_count = $counts[$value[$i]["source_address"]] ?? 0;
                    $model->security_events_id = $params[':id'] ?? "";
                    $model->iteration = $fieldVal2;
                    $model->flag = true;
                    $model->src_code = $value[$i]["destination_code"] ?? "";
                }
                $model->save(true);
            }
        }
    }

    /**
     * @param $counts
     * @param array $array
     * @param $key
     * @return array|null
     */
    private function array_group_byCustom(&$counts, array $array, $key) {
        if (!is_string($key)) {
            trigger_error('The key should be a string', E_USER_ERROR);
            return null;
        }

        // load the new array, splitting by the target key
        $grouped = [];
        foreach ($array as $value) {
            $_key = null;
            if (is_object($value) && property_exists($value, $key)) {
                $_key = $value->{$key};
            } elseif (isset($value[$key])) {
                $_key = $value[$key];
            }
            if ($_key === null) {
                $counts[$_key]=0;
                continue;
            }
            $grouped[$_key][] = $value;
            if (empty($counts[$_key])) {
                $counts[$_key] = 0;
            }
            // grouped count
            $counts[$_key]++;
        }

        // recursion for more grouping params
        if (func_num_args() > 3) {
            $args = func_get_args();
            foreach ($grouped as $key => $value) {
                $params = array_merge([ $value ], array_slice($args, 3, func_num_args()));
                $grouped[$key] = call_user_func_array('array_group_by', $params);
            }
        }

        return $grouped;
    }
}
