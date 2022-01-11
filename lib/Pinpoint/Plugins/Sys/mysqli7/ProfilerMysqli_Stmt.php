<?php
/*
 * User: eeliu
 * Date: 11/5/20
 * Time: 5:32 PM
 * changes: 2022-1-11: php8.1, the only way is extends all function
 */

namespace Pinpoint\Plugins\Sys\mysqli7;


class ProfilerMysqli_Stmt extends \mysqli_stmt
{
    protected $_instance;
    public function __construct(&$instance)
    {
        $this->_instance = &$instance;
    }

    public function bind_result (&...$vars): bool
    {
        return $this->_instance->bind_result(...$vars);
    }

    public function execute(?array $params = null):bool
    {
        $plugin = new StmtExecutePlugin("Stmt::execute",$this);
        try{
            $plugin->onBefore();
            $ret =  call_user_func([$this->_instance,'execute']);
            $plugin->onEnd($ret);
            return $ret;

        }catch (\Exception $e)
        {
            $plugin->onException($e);
        }
    }

    public function attr_get( $attribute): int
    {
        return call_user_func([$this->_instance,'attr_get'],$attribute);
    }

    public function attr_set( $attribute,  $value): bool
    {
        return call_user_func([$this->_instance,'attr_set'],$attribute,$value);
    }

    public function bind_param( $types,  &...$vars): bool
    {
        return $this->_instance->bind_param($types,$vars);
    }

    public function close(): bool
    {
        return $this->_instance->close();
    }
    public function data_seek($offset): void
    {
        $this->_instance->data_seek($offset);
    }

    public function fetch(): ?bool
    {
       return $this->_instance->fetch();
    }
    public function free_result(): void
    {
        $this->_instance->free_result();
    }

    public function get_result(): \mysqli_result
    {
        return $this->_instance->get_result();
    }

    public function get_warnings(): \mysqli_warning
    {
        return $this->_instance->get_warnings();
    }

    public function more_results(): bool
    {
        return $this->_instance->more_results();
    }
    public  function next_result(): bool
    {
        return $this->_instance->next_result();
    }
    public function num_rows(): int
    {
        return $this->_instance->num_rows();
    }
    public function prepare($query): bool
    {
        return $this->_instance->prepare($query);
    }
    public function reset(): bool
    {
        return $this->_instance->reset();
    }
    public function result_metadata(): \mysqli_result
    {
        return $this->_instance->result_metadata();
    }

    public function send_long_data( $param_num,  $data): bool
    {
        return $this->_instance->send_long_data($param_num,$data);
    }

    public function store_result(): bool
    {
        return $this->_instance->store_result();
    }

}
