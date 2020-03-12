            if(!isset($result))
                    {
                        $result = null;
                    }
                    if(isset($task['channel']))
                    {
                        $task['channel']->push([
                            'param'     =>  $param,
                            'result'    =>  $result,
                        ]);
                    }
                    else if(isset($task['callback']))
                    {
                        ($this->createCoCallable)(function() use($task, $param, $result){
                            $task['callback']($param, $result);
                        });
                    }
                }
            }
       