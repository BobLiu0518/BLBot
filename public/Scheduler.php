<?php

namespace BLBot;
class Scheduler {
    private $db, $name, $enabled, $validator, $runner, $interval, $timestamp;
    private function checkInterval() {
        return $this->timestamp - ($this->db->get($this->name)['lastExecute'] ?? 0) >= $this->interval;
    }
    private function setInterval() {
        return $this->db->set($this->name, ['lastExecute' => $this->timestamp]);
    }
    public function setTime(int $timestamp) {
        $this->timestamp = $timestamp;
    }
    public function validate(): bool {
        return $this->enabled && $this->checkInterval() && ($this->validator)($this->timestamp);
    }
    public function run(): void {
        $this->setInterval();
        try {
            ($this->runner)($this->timestamp);
        } catch (\Exception $e) {
            global $Queue;
            $time = date('Y/m/d H:i:s', $this->timestamp);
            $Queue[] = sendMaster("[{$time}] 执行 Scheduler {$this->name} 时发生错误：{$e}");
        }
    }
    public function __construct(string $name, bool $enabled, callable $validator, callable $runner, int $interval = -1) {
        $this->name = $name;
        $this->enabled = $enabled;
        $this->validator = $validator;
        $this->runner = $runner;
        $this->interval = $interval;
        $this->db = new Database('scheduler', ['key' => 'name']);
    }
}