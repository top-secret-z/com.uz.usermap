<select id="{$option->optionName}" name="values[{$option->optionName}]">
    {foreach from=$icons key=name item=link}
        <option value="{$name}"{if $name == $value} selected{/if}>{$name}</option>
    {/foreach}
</select>
