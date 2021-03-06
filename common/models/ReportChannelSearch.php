<?php

namespace common\models;

use DateInterval;
use DateTime;
use DateTimeZone;
use yii\data\ActiveDataProvider;
use yii\db\Query;


class ReportChannelSearch extends ReportChannelHourly
{
    public $time_zone;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['campaign_id', 'channel_id', 'time', 'clicks', 'unique_clicks', 'installs', 'match_installs', 'campaign_name', 'channel_name'], 'safe'],
            [['time_format', 'daily_cap', 'type', 'start', 'end', 'time_zone', 'om'], 'safe'],
            [['pay_out', 'adv_price'], 'number'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function hourlySearch($params)
    {

        $query = ReportChannelHourly::find();
        $query->alias('clh');
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'campaign_id' => [
                        'asc' => ['clh.campaign_id' => SORT_ASC],
                        'desc' => ['clh.campaign_id' => SORT_DESC],
                    ],
                    'channel_name' => [
                        'asc' => ['ch.username' => SORT_ASC],
                        'desc' => ['ch.username' => SORT_DESC],
                    ],
                    'campaign_name' => [
                        'asc' => ['cam.campaign_name' => SORT_ASC],
                        'desc' => ['cam.campaign_name' => SORT_DESC],
                    ],
                    'clicks' => [
                        'asc' => ['clicks' => SORT_ASC],
                        'desc' => ['clicks' => SORT_DESC],
                    ],
                    'cost' => [
                        'asc' => ['cost' => SORT_ASC],
                        'desc' => ['cost' => SORT_DESC],
                    ],
                    'revenue' => [
                        'asc' => ['revenue' => SORT_ASC],
                        'desc' => ['revenue' => SORT_DESC],
                    ],
                    'unique_clicks' => [
                        'asc' => ['unique_clicks' => SORT_ASC],
                        'desc' => ['unique_clicks' => SORT_DESC],
                    ],
                    'installs' => [
                        'asc' => ['installs' => SORT_ASC],
                        'desc' => ['installs' => SORT_DESC],
                    ],
                    'match_installs' => [
                        'asc' => ['match_installs' => SORT_ASC],
                        'desc' => ['match_installs' => SORT_DESC],
                    ],
                    'redirect_installs' => [
                        'asc' => ['redirect_installs' => SORT_ASC],
                        'desc' => ['redirect_installs' => SORT_DESC],
                    ],
                    'redirect_match_installs' => [
                        'asc' => ['redirect_match_installs' => SORT_ASC],
                        'desc' => ['redirect_match_installs' => SORT_DESC],
                    ],
                    'pay_out' => [
                        'asc' => ['pay_out' => SORT_ASC],
                        'desc' => ['pay_out' => SORT_DESC],
                    ],
                    'redirect_cost' => [
                        'asc' => ['redirect_cost' => SORT_ASC],
                        'desc' => ['redirect_cost' => SORT_DESC],
                    ],
                    'redirect_revenue' => [
                        'asc' => ['redirect_revenue' => SORT_ASC],
                        'desc' => ['redirect_revenue' => SORT_DESC],
                    ],
                    'adv_price' => [
                        'asc' => ['adv_price' => SORT_ASC],
                        'desc' => ['adv_price' => SORT_DESC],
                    ],
                ],
            ]
        ]);

        $this->load($params);
//        var_dump($this->type);
//        die();
        if (!$this->validate()) {
            return $dataProvider;
        }
//        $a = strtotime($this->start);
        $start = new DateTime($this->start, new DateTimeZone($this->time_zone));
        $end = new DateTime($this->end, new DateTimeZone($this->time_zone));
        $end = $end->add(new DateInterval('P1D'));
        $start = $start->getTimestamp();
        $end = $end->getTimestamp();
        $query->select([
            'ch.username channel_name',
            'cam.campaign_name campaign_name',
            'clh.campaign_id',
            'clh.channel_id',
            'clh.time timestamp',
            'clh.time_format',
            'clh.clicks',
            'clh.unique_clicks',
            'clh.installs',
            'clh.redirect_installs',
            'clh.match_installs',
            'clh.redirect_match_installs',
            'clh.pay_out',
            'clh.adv_price',
            'clh.cost',
            'clh.redirect_cost',
            'clh.redirect_revenue',
            'clh.revenue',
            'cam.daily_cap cap',
            'clh.daily_cap',
            'u.username om',
        ]);
        $query->joinWith('channel ch');
        $query->joinWith('campaign cam');
        $query->leftJoin('user u', 'ch.om = u.id');
        // grid filtering conditions
        $query->andFilterWhere([
            'campaign_id' => $this->campaign_id,
            'channel_id' => $this->channel_id,
            'pay_out' => $this->pay_out,
            'adv_price' => $this->adv_price,
            'ch.username' => $this->channel_name,
        ]);

        $query->andFilterWhere(['like', 'time_format', $this->time_format])
            ->andFilterWhere(['like', 'ch.username', $this->channel_name])
            ->andFilterWhere(['like', 'cam.campaign_name', $this->campaign_name])
            ->andFilterWhere(['>=', 'time', $start])
            ->andFilterWhere(['<', 'time', $end]);
        if (\Yii::$app->user->can('admin')) {
            $query->andFilterWhere(['like', 'u.username', $this->om]);
        } else {
            $query->andFilterWhere(['ch.om' => \Yii::$app->user->id]);

        }

        if ($dataProvider->getSort()->getOrders() == null) {
            $query->orderBy(['ch.username' => SORT_ASC, 'cam.campaign_name' => SORT_ASC, 'time' => SORT_DESC]);
        }
//                var_dump(strtotime($this->start));
//        var_dump(strtotime($this->end));
//        var_dump($query->createCommand()->sql);
//        die();
        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function dailySearch($params)
    {

//        $query = ReportChannelHourly::find();
        $query = new Query();
//        $query->alias('clh');
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'attributes' => [
                    'campaign_id' => [
                        'asc' => ['clh.campaign_id' => SORT_ASC],
                        'desc' => ['clh.campaign_id' => SORT_DESC],
                    ],
                    'channel_name' => [
                        'asc' => ['ch.username' => SORT_ASC],
                        'desc' => ['ch.username' => SORT_DESC],
                    ],
                    'campaign_name' => [
                        'asc' => ['cam.campaign_name' => SORT_ASC],
                        'desc' => ['cam.campaign_name' => SORT_DESC],
                    ],
                    'clicks' => [
                        'asc' => ['clicks' => SORT_ASC],
                        'desc' => ['clicks' => SORT_DESC],
                    ],
                    'cost' => [
                        'asc' => ['cost' => SORT_ASC],
                        'desc' => ['cost' => SORT_DESC],
                    ],
                    'revenue' => [
                        'asc' => ['revenue' => SORT_ASC],
                        'desc' => ['revenue' => SORT_DESC],
                    ],
                    'unique_clicks' => [
                        'asc' => ['unique_clicks' => SORT_ASC],
                        'desc' => ['unique_clicks' => SORT_DESC],
                    ],
                    'installs' => [
                        'asc' => ['installs' => SORT_ASC],
                        'desc' => ['installs' => SORT_DESC],
                    ],
                    'match_installs' => [
                        'asc' => ['match_installs' => SORT_ASC],
                        'desc' => ['match_installs' => SORT_DESC],
                    ],
                    'redirect_installs' => [
                        'asc' => ['redirect_installs' => SORT_ASC],
                        'desc' => ['redirect_installs' => SORT_DESC],
                    ],
                    'redirect_match_installs' => [
                        'asc' => ['redirect_match_installs' => SORT_ASC],
                        'desc' => ['redirect_match_installs' => SORT_DESC],
                    ],
                    'pay_out' => [
                        'asc' => ['pay_out' => SORT_ASC],
                        'desc' => ['pay_out' => SORT_DESC],
                    ],
                    'redirect_cost' => [
                        'asc' => ['redirect_cost' => SORT_ASC],
                        'desc' => ['redirect_cost' => SORT_DESC],
                    ],
                    'redirect_revenue' => [
                        'asc' => ['redirect_revenue' => SORT_ASC],
                        'desc' => ['redirect_revenue' => SORT_DESC],
                    ],
                    'adv_price' => [
                        'asc' => ['adv_price' => SORT_ASC],
                        'desc' => ['adv_price' => SORT_DESC],
                    ],
                ],
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
//        var_dump(strtotime($this->start . '-1 day'));
//        var_dump(strtotime($this->end . '+1 day'));
        $start = new DateTime($this->start, new DateTimeZone($this->time_zone));
        $end = new DateTime($this->end, new DateTimeZone($this->time_zone));
//        $start = $start->sub(new DateInterval('P1D'));
        $end = $end->add(new DateInterval('P1D'));
        $start = $start->getTimestamp();
        $end = $end->getTimestamp();
        $query->select([
            'ch.username channel_name',
            'cam.campaign_name campaign_name',
            'clh.campaign_id',
            'clh.channel_id',
//            'FROM_UNIXTIME(clh.time,"%Y-%m-%d") time',
            'UNIX_TIMESTAMP(FROM_UNIXTIME(clh.time, "%Y-%m-%d")) timestamp',
            'SUM(clh.clicks) clicks',
            'SUM(clh.unique_clicks) unique_clicks',
            'SUM(clh.installs) installs',
            'SUM(clh.match_installs) match_installs',
            'SUM(clh.redirect_installs) redirect_installs',
            'SUM(clh.redirect_match_installs) redirect_match_installs',
            'AVG(clh.pay_out) pay_out',
            'AVG(clh.adv_price) adv_price',
            'SUM(clh.cost) cost',
            'SUM(clh.revenue) revenue',
            'SUM(clh.redirect_cost) redirect_cost',
            'SUM(clh.redirect_revenue) redirect_revenue',
            'u.username om',
            'cam.daily_cap cap',
            'clh.daily_cap',

        ]);
        $query->from('campaign_log_hourly clh');
        $query->leftJoin('channel ch', 'clh.channel_id = ch.id');
        $query->leftJoin('campaign cam', 'clh.campaign_id = cam.id');
        $query->leftJoin('user u', 'ch.om = u.id');
        // grid filtering conditions
        $query->andFilterWhere([
            'clh.campaign_id' => $this->campaign_id,
            'clh.channel_id' => $this->channel_id,
            'clh.pay_out' => $this->pay_out,
            'clh.adv_price' => $this->adv_price,
            'ch.username' => $this->channel_name,
        ]);

        $query->andFilterWhere(['like', 'ch.username', $this->channel_name])
            ->andFilterWhere(['like', 'cam.campaign_name', $this->campaign_name])
            ->andFilterWhere(['>=', 'time', $start])
            ->andFilterWhere(['<', 'time', $end]);

        if (\Yii::$app->user->can('admin')) {
            $query->andFilterWhere(['like', 'u.username', $this->om]);
        } else {
            $query->andFilterWhere(['ch.om' => \Yii::$app->user->id]);
        }
        $query->groupBy([
            'clh.campaign_id',
            'clh.channel_id',
            'timestamp',
        ]);

        if ($dataProvider->getSort()->getOrders() == null) {
            $query->orderBy(['ch.username' => SORT_ASC, 'cam.campaign_name' => SORT_ASC, 'time' => SORT_DESC]);
        }
//        var_dump(strtotime($this->start));
//        var_dump(strtotime($this->end));
//        var_dump($query->createCommand()->sql);
//        die();
        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function summarySearch($params)
    {
        $query = new Query();
//        $query->alias('clh');
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $start = new DateTime($this->start, new DateTimeZone($this->time_zone));
        $end = new DateTime($this->end, new DateTimeZone($this->time_zone));
        $end = $end->add(new DateInterval('P1D'));
        $start = $start->getTimestamp();
        $end = $end->getTimestamp();
        $query->select([
            'SUM(clh.clicks) clicks',
            'SUM(clh.unique_clicks) unique_clicks',
            'SUM(clh.installs) installs',
            'SUM(clh.match_installs) match_installs',
            'SUM(clh.cost) cost',
            'SUM(clh.revenue) revenue',
            'SUM(clh.redirect_installs) redirect_installs',
            'SUM(clh.redirect_match_installs) redirect_match_installs',
            'SUM(clh.redirect_cost) redirect_cost',
            'SUM(clh.redirect_revenue) redirect_revenue',

        ]);
        $query->from('campaign_log_hourly clh');
        $query->leftJoin('channel ch', 'clh.channel_id = ch.id');
        $query->leftJoin('campaign cam', 'clh.campaign_id = cam.id');
        $query->leftJoin('user u', 'ch.om = u.id');
        // grid filtering conditions
        $query->andFilterWhere([
            'campaign_id' => $this->campaign_id,
            'channel_id' => $this->channel_id,
            'pay_out' => $this->pay_out,
            'adv_price' => $this->adv_price,
            'ch.username' => $this->channel_name,
        ]);

        $query->andFilterWhere(['like', 'ch.username', $this->channel_name])
            ->andFilterWhere(['like', 'cam.campaign_name', $this->campaign_name])
            ->andFilterWhere(['>=', 'time', $start])
            ->andFilterWhere(['<', 'time', $end]);

        if (\Yii::$app->user->can('admin')) {
            $query->andFilterWhere(['like', 'u.username', $this->om]);
        } else {
            $query->andFilterWhere(['ch.om' => \Yii::$app->user->id]);
        }
        return $dataProvider;
    }

    public function sumSearch($params)
    {


//        $query = ReportChannelHourly::find();
        $query = new Query();
//        $query->alias('clh');
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'attributes' => [
                    'campaign_id' => [
                        'asc' => ['clh.campaign_id' => SORT_ASC],
                        'desc' => ['clh.campaign_id' => SORT_DESC],
                    ],
                    'channel_name' => [
                        'asc' => ['ch.username' => SORT_ASC],
                        'desc' => ['ch.username' => SORT_DESC],
                    ],
                    'campaign_name' => [
                        'asc' => ['cam.campaign_name' => SORT_ASC],
                        'desc' => ['cam.campaign_name' => SORT_DESC],
                    ],
                    'clicks' => [
                        'asc' => ['clicks' => SORT_ASC],
                        'desc' => ['clicks' => SORT_DESC],
                    ],
                    'cost' => [
                        'asc' => ['cost' => SORT_ASC],
                        'desc' => ['cost' => SORT_DESC],
                    ],
                    'revenue' => [
                        'asc' => ['revenue' => SORT_ASC],
                        'desc' => ['revenue' => SORT_DESC],
                    ],
                ],
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $start = new DateTime($this->start, new DateTimeZone($this->time_zone));
        $end = new DateTime($this->end, new DateTimeZone($this->time_zone));
        $end = $end->add(new DateInterval('P1D'));
        $start = $start->getTimestamp();
        $end = $end->getTimestamp();
        $query->select([
            'ch.username channel_name',
            'cam.campaign_name campaign_name',
            'clh.campaign_id',
            'clh.channel_id',
            'SUM(clh.clicks) clicks',
            'SUM(clh.unique_clicks) unique_clicks',
            'SUM(clh.installs) installs',
            'SUM(clh.match_installs) match_installs',
            'SUM(clh.redirect_installs) redirect_installs',
            'SUM(clh.redirect_match_installs) redirect_match_installs',
            'AVG(clh.pay_out) pay_out',
            'AVG(clh.adv_price) adv_price',
            'SUM(clh.cost) cost',
            'SUM(clh.revenue) revenue',
            'SUM(clh.redirect_cost) redirect_cost',
            'SUM(clh.redirect_revenue) redirect_revenue',
            'u.username om',
            'cam.daily_cap cap',
            'clh.daily_cap',

        ]);
        $query->from('campaign_log_hourly clh');
        $query->leftJoin('channel ch', 'clh.channel_id = ch.id');
        $query->leftJoin('campaign cam', 'clh.campaign_id = cam.id');
        $query->leftJoin('user u', 'ch.om = u.id');
        // grid filtering conditions
        $query->andFilterWhere([
            'clh.campaign_id' => $this->campaign_id,
            'clh.channel_id' => $this->channel_id,
            'clh.pay_out' => $this->pay_out,
            'clh.adv_price' => $this->adv_price,
            'ch.username' => $this->channel_name,
        ]);

        $query->andFilterWhere(['like', 'ch.username', $this->channel_name])
            ->andFilterWhere(['like', 'cam.campaign_name', $this->campaign_name])
            ->andFilterWhere(['>=', 'time', $start])
            ->andFilterWhere(['<', 'time', $end]);

        if (\Yii::$app->user->can('admin')) {
            $query->andFilterWhere(['like', 'u.username', $this->om]);
        } else {
            $query->andFilterWhere(['ch.om' => \Yii::$app->user->id]);
        }
        $query->groupBy([
            'clh.campaign_id',
            'clh.channel_id',
        ]);

        if ($dataProvider->getSort()->getOrders() == null) {
            $query->orderBy(['ch.username' => SORT_ASC, 'cam.campaign_name' => SORT_ASC, 'time' => SORT_DESC]);
        }
        return $dataProvider;
    }
}
