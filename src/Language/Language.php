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
    const CHILD_PROCESS_EXIT_ABNORMALLY = 'child_process_exit_abnormally';
    const WORKER_EXIT_ABNORMALLY = 'worker_exit_abnormally';
    const TASKER_EXIT_ABNORMALLY = 'takser_exit_abnormally';
    const NEW_PROCESS_INFO = 'new_process_info';
    const TASKER_NOT_DONE_MSG_TO_CHANNEL = 'tasker_not_done_msg_to_channel';
    const RESTART_SUCCESS_WITH_PROCESS_ABNORMALLY = 'restart_success_with_process_abnormally';
    const RESTART_FAIL_WITH_PROCESS_ABNORMALLY = 'restart_fail_with_process_abnormally';
    const CREATE_MEMORY_TABLE_SUCCESS = 'create_memory_table_success';
    const CREATE_MEMORY_TABLE_FAIL = 'create_memory_table_fail';
    const CREATE_TASKER_TABLE_SUCCESS = 'create_takser_table_success';
    const CREATE_TASKER_TABLE_FAIL = 'create_takser_table_fail';
    const CREATE_COLLECT_TABLE_SUCCESS = 'create_collect_table_success';
    const CREATE_COLLECT_TABLE_FAIL = 'create_collect_table_fail';
    const CREATE_MARK_TABLE_SUCCESS = 'create_mark_table_success';
    const CREATE_MARK_TABLE_FAIL = 'create_mark_table_fail';
    const GET_MSG_TO_TASKER = 'get_msg_to_tasker';
    const TASKER_RUNNING_STATUS = 'tasker_running_status';
    const SINGLE_TASKER_MEMORY_STATUS = 'single_tasker_memory_status';
    const SINGLE_TASKER_MOMERY_LIMIT = 'single_tasker_memory_limit';
    const RESTART_MANAGER = 'restart_manager';
    const SINGLE_MANAGER_MEMORY_STATUS = 'single_manager_memory_status';
    const SINGLE_MANAGER_MEMORY_LIMIT = 'single_manager_memory_limit';

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
        ],
        self::CHILD_PROCESS_EXIT_ABNORMALLY => [
            'cn' => '有子进程异常退出',
            'en' => 'There is a child process abnormal exit'
        ],
        self::WORKER_EXIT_ABNORMALLY => [
            'cn' => '异常退出的进程是worker进程',
            'en' => 'Exceptional exit process is worker process'
        ],
        self::TASKER_EXIT_ABNORMALLY => [
            'cn' => '异常退出的进程是tasker进程',
            'en' => 'Exceptional exit process is tasker process'
        ],
        self::NEW_PROCESS_INFO => [
            'cn' => '新重启的进程信息',
            'en' => 'New restart process information'
        ],
        self::TASKER_NOT_DONE_MSG_TO_CHANNEL => [
            'cn' => '发现tasker进程异常退出时存在未完成的event,放入重试队列',
            'en' => 'Found that the tasker process abnormal exit when there is an incomplete event, put the retry queue'
        ],
        self::RESTART_SUCCESS_WITH_PROCESS_ABNORMALLY => [
            'cn' => '异常退出的进程重启成功',
            'en' => 'Exceptional exit process restarted successfully'
        ],
        self::RESTART_FAIL_WITH_PROCESS_ABNORMALLY => [
            'cn' => '异常退出的进程重启失败',
            'en' => 'Exception exited the process restart failed'
        ],
        self::CREATE_MEMORY_TABLE_SUCCESS => [
            'cn' => '创建内存统计表成功',
            'en' => 'Create a memory statistics table successfully'
        ],
        self::CREATE_MEMORY_TABLE_FAIL => [
            'cn' => '创建内存统计表失败',
            'en' => 'Failed to create memory statistics table'
        ],
        self::CREATE_TASKER_TABLE_SUCCESS => [
            'cn' => '创建tasker进程表成功',
            'en' => 'Create tasker process table success'
        ],
        self::CREATE_TASKER_TABLE_FAIL => [
            'cn' => '创建tasker进程表失败',
            'en' => 'Failed to create tasker process table'
        ],
        self::CREATE_COLLECT_TABLE_SUCCESS => [
            'cn' => '创建通信统计表成功',
            'en' => 'Create a communication statistics table successfully'
        ],
        self::CREATE_COLLECT_TABLE_FAIL => [
            'cn' => '创建通信统计表失败',
            'en' => 'Failed to create communication statistics'
        ],
        self::CREATE_MARK_TABLE_SUCCESS => [
            'cn' => '创建标记内存表成功',
            'en' => 'Create markup memory table successfully'
        ],
        self::CREATE_MARK_TABLE_FAIL => [
            'cn' => '创建标记内存表失败',
            'en' => 'Failed to create tag memory table'
        ],
        self::GET_MSG_TO_TASKER => [
            'cn' => '获取到event,发送给tasker',
            'en' => 'Get Event, Send To Tasker'
        ],
        self::TASKER_RUNNING_STATUS => [
            'cn' => '当前进程运行状态:%s',
            'en' => 'The current process is running:%s'
        ],
        self::SINGLE_TASKER_MEMORY_STATUS => [
            'cn' => '当前tasker进程内存状态',
            'en' => 'Current tasker state memory state'
        ],
        self::SINGLE_TASKER_MOMERY_LIMIT => [
            'cn' => '当前tasker进程内存已超过限制值:%sMB，自杀重启',
            'en' => 'The current tasker process memory has exceeded the limit value: %sMB, suicide restart'
        ],
        self::RESTART_MANAGER => [
            'cn' => '重启当前manager进程',
            'en' => 'Restart the current manager process'
        ],
        self::SINGLE_MANAGER_MEMORY_STATUS => [
            'cn' => '当前manager进程内存使用情况',
            'en' => 'Current manager process memory usage'
        ],
        self::SINGLE_MANAGER_MEMORY_LIMIT => [
            'cn' => '当前manager进程内存已超过限制值:%sMB，需要进行重启',
            'en' => 'The current manager process memory has exceeded the limit value :%s MB and needs to be restarted'
        ]
    ];





    public static function getWord($key)
    {
        return self::MAP[$key][SwooleMaster::getConfig()->getLanguage()];
    }












}