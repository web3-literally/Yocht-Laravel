<template>
    <div class="array-input mt-3">
        <div class="array-input-values">
            <div class="form-group array-input-value mb-1" v-for="(item, index) in values">
                <div class="d-flex flex-row align-items-center">
                    <input :name="'options[' + index + ']'" class="form-control" v-model="item.value" :key="index">
                    <a href="#" class="link link--red ml-3" @click="remove(index)"><i class="far fa-trash-alt"></i></a>
                </div>
            </div>
        </div>
        <button type="button" class="mt-2 btn btn--orange" @click="add">
            Add a value
        </button>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                values: []
            };
        },
        methods: {
            add: function () {
                this.values.push({
                    value: ''
                });

                return false;
            },
            remove: function (e, index) {
                this.values.splice(index, 1);

                return false;
            }
        }
    }
</script>
