<?php

namespace Jue\Swoole\Language;

use Jue\Swoole\Achieves\Masters\SwooleMaster;

class Language
{

    const WORKER_ERROR = 'worker_error';
    const TASKER_ERROR = 'tasker_error';
    const WORKER_START = 'worker_start';
    const TASKER_START = 'tasker_start';
    const TASKER_GET_MESSAGE = 'tasker_get_message';
    const TASKER_MESSAGE_ERROR = 'tasker_message_error';
    const TASKER_MEMORY_STATUS = 'tasker_memory_status';
    const TASKER_PROCESS_ERROR = 'tasker_process_error';
    const TASKER_MESSAGE_TO_CHANNEL = 'tasker_message_to_channel';
    const TASKER_PROCESS_SUCCESS = 'tasker_process_success';
    const TASKER_PROCESS_FAIL = 'tasker_process_fail';
    const MEMORY_TABLE_CLEAR = 'memory_table_clear';
    const MARK_TABLE_SET = 'mark_table_set';
    const MARK_TABLE_CLEAR = 'mark_table_clear';
    const COLLECT_TABLE_INCR = 'collect_table_incr';
    const COLLECT_TABLE_DECR = 'collect_table_decr';
    const USED_LISTENER = 'used_listener';
    const CLEAN_MANAGER = 'clean_manager';
    const SEND_CHANNEL_SUCCESS = 'send_channel_success';
    const SEND_CHANNEL_FAIL = 'send_channel_fail';
    const SELECT_TASKER = 'select_tasker';
    const CLIENT_SEND_MSG_SUCCESS = 'client_send_msg_success';
    const CLIENT_SEND_MSG_FAIL = 'client_send_msg_fail';
    const INIT_MANAGER_TASKER = 'init_manager_tasker';
    const INIT_MANAGER_TASKER_OK = 'init_manager_tasker_ok';
    const INIT_MANAGER_WORKER = 'init_manager_worker';
    const INIT_MANAGER_WORKER_OK = 'init_manager_worker_ok';
    const EXIT_WORKER = 'exit_worker';
    const EXIT_WORKER_DONE = 'exit_worker_done';
    const EXIT_TASKER = 'exit_tasker';
    const EXIT_TASKER_DONE = 'exit_tasker_done';
    const CHILD_PROCESS_DONE = 'child_process_done';
    const ALL_EXIT = 'all_exit';

    const MAP = [
        self::WORKER_START => [
            'cn' => '当前启动了worker进程',
            'en' => 'Worker is started'
        ],
        self::WORKER_ERROR => [
            'cn' => 'worker进程出错',
            'en' => 'Worker error!!'
        ],
        self::TASKER_START => [
            'cn' => '当前启动了tasker进程',
            'en' => 'Tasker is started'
        ],
        self::TASKER_GET_MESSAGE => [
            'cn' => '从worker获取到信息,tasker开始工作',
            'en' => 'Get Msg From Worker, tasker processing'
        ],
        self::TASKER_MESSAGE_ERROR => [
            'cn' => 'tasker获取到的消息无法转化为event',
            'en' => 'Tasker Msg has error, not event'
        ],
        self::TASKER_MEMORY_STATUS => [
            'cn' => '当前tasker进程内存使用情况',
            'en' => 'Tasker Memory Status'
        ],
        self::TASKER_PROCESS_ERROR => [
            'cn' => 'tasker进程处理过程出现异常,记录该异常',
            'en' => 'Tasker process error, logging the error'
        ],
        self::TASKER_MESSAGE_TO_CHANNEL => [
            'cn' => 'tasker处理event失败,发送进channel等待重复处理',
            'en' => 'Tasker process event is error, send the event to channel'
        ],
        self::TASKER_ERROR => [
            'cn' => 'tasker进程出现异常',
            'en' => 'Tasker is error'
        ],
        self::TASKER_PROCESS_FAIL => [
            'cn' => 'tasker处理消息失败',
            'en' => 'Tasker process fail'
        ],
        self::TASKER_PROCESS_SUCCESS => [
            'cn' => 'tasker处理消息成功',
            'en' => 'Tasker process success'
        ],
        self::MEMORY_TABLE_CLEAR => [
            'cn' => '当前在内存table中clear',
            'en' => 'Memory_table clear'
        ],
        self::MARK_TABLE_SET => [
            'cn' => '当前在标记table中种下了标记',
            'en' => 'Mark_table set'
        ],
        self::MARK_TABLE_CLEAR => [
            'cn' => '当前在标记table中清除了标记',
            'en' => 'Mark_table clear'
        ],
        self::COLLECT_TABLE_INCR => [
            'cn' => '统计table——incr操作记录',
            'en' => 'Collect_table incr'
        ],
        self::COLLECT_TABLE_DECR => [
            'cn' => '统计table——decr操作记录',
            'en' => 'Collect_table decr'
        ],
        self::USED_LISTENER => [
            'cn' => '使用的listener',
            'en' => 'Used Listener'
        ],
        self::CLEAN_MANAGER => [
            'cn' => '清洗可能之前存在的僵死进程',
            'en' => 'Cleaning may exist before the dead process'
        ],
        self::SEND_CHANNEL_SUCCESS => [
            'cn' => '发送进channel成功',
            'en' => 'Send Channel Success'
        ],
        self::SEND_CHANNEL_FAIL => [
            'cn' => '发送进channel失败',
            'en' => 'Send Channel Fail'
        ],
        self::SELECT_TASKER => [
            'cn' => '选择给对应的tasker发送消息',
            'en' => 'Select to send a message to the corresponding tasker'
        ],
        self::CLIENT_SEND_MSG_FAIL => [
            'cn' => '发送消息失败',
            'en' => 'Send Msg Fail'
        ],
        self::CLIENT_SEND_MSG_SUCCESS => [
            'cn' => '发送消息成功',
            'en' => 'Send Msg Success'
        ],
        self::INIT_MANAGER_TASKER => [
            'cn' => '初始化swoole-manager-taskers',
            'en' => 'init swoole-manager-taskers'
        ],
        self::INIT_MANAGER_TASKER_OK => [
            'cn' => '初始化swoole-manager-taskers完成',
            'en' => 'init swoole-manager-taskers ok'
        ],
        self::INIT_MANAGER_WORKER => [
            'cn' => '初始化swoole-manager-worker',
            'en' => 'init swoole-manager-worker'
        ],
        self::INIT_MANAGER_WORKER_OK => [
            'cn' => '初始化swoole-manager-worker完成',
            'en' => 'init swoole-manager-worker ok'
        ],
        self::EXIT_WORKER => [
            'cn' => 'swoole开始退出worker进程',
            'en' => 'Start to exit the worker process'
        ],
        self::EXIT_WORKER_DONE => [
            'cn' => 'swoole检测到所有worker进程退出完成',
            'en' => 'All worker processes exit'
        ],
        self::EXIT_TASKER => [
            'cn' => 'swoole开始退出tasker进程',
            'en' => 'Start to exit the tasker process'
        ],
        self::EXIT_TASKER_DONE => [
            'cn' => 'swoole检测到所有tasker进程退出完成',
            'en' => 'All tasker processes exit'
        ],
        self::CHILD_PROCESS_DONE => [
            'cn' => 'swoole检测到所有子进程退出完成',
            'en' => 'All child processes are finished'
        ],
        self::ALL_EXIT => [
            'cn' => 'swoole所有进程退出完成',
            'en' => 'all exit'
        ]
    ];





    public static function getWord($key)
    {
        return self::MAP[$key][SwooleMaster::getConfig()->getLanguage()];
    }












}