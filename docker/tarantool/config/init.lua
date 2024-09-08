box.cfg{
    wal_mode = 'none',  -- Отключаем WAL, чтобы не записывать изменения на диск
    memtx_memory = 8 * 1024 * 1024 * 1024,  -- 8ГБ памяти для работы с данными
    checkpoint_interval = 0,  -- Отключаем автоматическое создание снапшотов
    checkpoint_count = 1,  -- Устанавливаем минимальное значение, разрешённое системой
    force_recovery = true  -- Разрешаем загрузку данных без проверок
}


box.schema.space.create('dialogs', {
    format = {
        {name = 'id', type = 'unsigned'},
        {name = 'user_id_1', type = 'unsigned'},
        {name = 'user_id_2', type = 'unsigned'},
        {name = 'created_at', type = 'unsigned'},
        {name = 'updated_at', type = 'unsigned'}
    },
    if_not_exists = true
})

box.space.dialogs:create_index('primary', {
    parts = {'id'},
    if_not_exists = true
})

box.space.dialogs:create_index('user_ids', {
    parts = {'user_id_1', 'user_id_2'},
    unique = true,
    if_not_exists = true
})

-- Создание пространства для сообщений
box.schema.space.create('messages', {
    format = {
        {name = 'id', type = 'unsigned'},
        {name = 'dialog_id', type = 'unsigned'},
        {name = 'sender_id', type = 'unsigned'},
        {name = 'text', type = 'string'},
        {name = 'created_at', type = 'unsigned'},
        {name = 'updated_at', type = 'unsigned'}
    },
    if_not_exists = true
})

box.space.messages:create_index('primary', {
    parts = {'id'},
    if_not_exists = true
})

box.space.messages:create_index('dialog_id', {
    parts = {'dialog_id'},
    unique = false,
    if_not_exists = true
})

box.space.messages:create_index('sender_id', {
    parts = {'sender_id'},
    unique = false,
    if_not_exists = true
})

box.space.messages:create_index('created_at', {
    parts = {'created_at'},
    unique = false,
    if_not_exists = true
})

box.schema.sequence.create('msg_id', {min = 1, start = 1, if_not_exists = true})
box.schema.sequence.create('dialogs', {min = 1, start = 1, if_not_exists = true})


function get_or_create_dialog(user_id_1, user_id_2)
    local dialog = box.space.dialogs.index.user_ids:get{user_id_1, user_id_2}
    if dialog == nil then
        local id = box.sequence.dialogs:next()
        box.space.dialogs:insert{id, user_id_1, user_id_2, os.time(), os.time()}
        dialog = box.space.dialogs.index.user_ids:get{user_id_1, user_id_2}
    end
    return dialog
end


function get_dialog_id(user_id_1, user_id_2)
    local dialog = box.space.dialogs.index.user_ids:get{user_id_1, user_id_2}
    if dialog ~= nil then
        return dialog.id
    end
    return nil
end

function add_message(dialog_id, sender_id, text)
    local id = box.sequence.msg_id:next()
    box.space.messages:insert{id, dialog_id, sender_id, text, os.time(), os.time()}
    return id
end

function get_messages(dialog_id, offset, limit)
    local messages = box.space.messages.index.dialog_id:select(dialog_id, {offset = offset, limit = limit})
    return messages
end
