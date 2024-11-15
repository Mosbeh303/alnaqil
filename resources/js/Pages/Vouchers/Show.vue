<template>
    <div >
        <Head title="السند" />

        <page-header class="no-print">
            <p >{{$page.props.language.the_bond}}</p>


            <Button class="md:absolute  bottom-0" :class="{ 'md:left-7': $page.props.locale == 'ar', 'md:right-7': $page.props.locale == 'en' }" @click="print()">
                {{$page.props.language.print_the_bond}}
            </Button>
        </page-header>

        <Section class="md:mx-auto text-md  lg:my-7 max-w-5xl  !relative text-gray-700 !pb-28 print rtl" dir="rtl">
            <div class="flex gap-4 absolute top-0 right-0 h-8 w-full">
                <div class="w-2/12 h-6 rounded-tr" style="background: #64C7AA"> </div>
                <div class="w-4/12 h-6" style="background: #676CE6"></div>
            </div>

            <div class="flex gap-4 absolute bottom-8 right-0 h-8 w-full">
                <div class="w-9/12  h-6" style="background: #676CE6"></div>
                <div class="w-3/12  h-6 " style="background: #64C7AA"></div>
            </div>

            <div class="absolute top-14 text-justify">
                <p class="text-xl ">شركة الناقل المحلي لنقل الطرود </p>
                <p class="text-base">Alnaqil Almahalli Company for transport parcel</p>
                <p class="mt-2">س.ت  1010586594  C.R</p>
            </div>

            <ApplicationLogo class="w-48 h-auto absolute top-8 left-8" />

            <div class="mt-20 flex flex-col justify-center items-center w-full text-blue-900">
                <h1 class="font-bold text-xl ">
                    <span v-if="voucher.amount < 0 || voucher.type === 'operator' || voucher.type === 'empReimbursement'">سند قبض</span>
                    <span v-else>سند صرف</span>
                </h1>
                <h1 class="font-bold text-xl">

                    <span v-if="voucher.amount < 0 || voucher.type === 'operator' || voucher.type === 'empReimbursement'">RECEIPT VOUCHER</span>
                    <span v-else>PAYMENT VOUCHER</span>
                </h1>
                 <div class="mt-4 text-center text-3xl font-bold text-red-600">
                    <span v-if="voucher.type === 'operator'" v-html="'M' + voucher.number"></span>
                    <span v-else-if="voucher.type === 'empReimbursement'" v-html="'MA' + voucher.number"></span>
                    <span v-else  v-html="'00' + voucher.number"></span>
                </div>
            </div>

            <div class="mt-4 flex  justify-between relative bottom-10">
                <div class="flex gap-4">
                    <p class="font-bold ">التاريخ: </p>
                    <p>{{voucher.date}}</p>
                </div>

                <div></div>
            </div>

            <div>
                <div class="flex justify-between">
                    <p class=" ">
                        <span v-if="voucher.amount < 0 || voucher.type === 'operator' || voucher.type === 'empReimbursement'">استلمنا من السيد / السادة:</span>
                        <span v-else>اصرفوا للسيد / السادة:</span>
                    </p>
                    <p class="font-bold" v-if="voucher.storeName != null" > {{voucher.storeName}}</p>
                    <p class="font-bold" v-else >  {{voucher.name}}  </p>
                    <p class=" ">
                        <span v-if="voucher.amount < 0 || voucher.type === 'operator' || voucher.type === 'empReimbursement'">:Received from Mrs</span>
                        <span v-else>:Pay to Mrs</span>
                    </p>
                </div>

                <div class="flex justify-between mt-4">
                    <p class=" ">مبلغ وقدره: </p>
                    <p class="font-bold"> {{Math.abs(voucher.amount)}} ريال .S.R </p>
                    <p class=" ">:Amount </p>
                </div>

                <div class="flex mt-4 gap-8">
                    <div class="flex justify-between flex-grow">
                        <p class=" ">نقدا/ شيك رقم: </p>
                        <p class="font-bold">  </p>
                        <p class=" ">:Cash/ Cheque No </p>
                    </div>

                    <div class="flex justify-between flex-grow">
                        <p class=" ">على بنك: </p>
                        <p class="font-bold">
                            <span v-if=" voucher.type === 'operator'">{{voucher.to_bank}}</span>
                            <span v-else>{{voucher.bank}}</span>
                        </p>
                        <p class=" ">:Bank</p>
                    </div>
                </div>

                <div class="flex justify-between mt-4">
                    <p class=" ">وذلك مقابل: </p>
                    <p class="font-bold"> {{voucher.notice}}</p>
                    <p class=" ">:Being </p>
                </div>

                <div class="flex mt-8 gap-8">
                    <div class="flex justify-between flex-grow">
                        <p class=" ">المدير </p>
                        <p class="font-bold text-xs text-gray-500"> .....................................</p>
                        <p class=" ">Manager</p>
                    </div>
                    <div class="flex justify-between flex-grow">
                        <p class=" ">المحاسب </p>
                        <p class="font-bold text-xs text-gray-500"> .....................................</p>
                        <p class=" ">Accountant </p>
                    </div>

                    <div class="flex justify-between flex-grow">
                        <p class=" ">المستلم </p>
                        <p class="font-bold text-xs text-gray-500"> .....................................</p>
                        <p class=" ">Receiver</p>
                    </div>


                </div>
                <p class="text-xs absolute bottom-20 text-gray-500">* هذا السند إلكتروني لا يحتاج لتوقيع</p>
            </div>

            <div class="absolute bottom-2 right-0 text-sm text-center w-full">
                الرياض - العزيزية
                شارع سفيان بن عوف
                0590700745 - 8001240316

            </div>

        </Section>



    </div>

</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import PageHeader from "@/Components/PageHeader.vue";
import Section from "@/Components/Section.vue";
import Button from "@/Components/Button.vue";
import { Head, Link } from "@inertiajs/vue3";

export default {
    layout: BreezeAuthenticatedLayout,

    props:{
        voucher: Object,
    },

    components:{
        PageHeader,
        Button,
        Head,
        Link,
        Section,
        ApplicationLogo
    },

    methods:{
        print(){
            document.title = this.voucher.id;
            window.print();
            window.print();
        }
    }
};
</script>

<style>
@media print {



    .no-print{
        display: none;
    }

    .print{
        font-size: 14px!important;
    }

    .text-sm{
        font-size: 12px !important;
    }

    .text-base{
        font-size: 13.5px !important;
    }

    @page{
        size: A5 landscape !important;
        margin:2mm;
    }

    /* .voucher{
        width: 100%!important;
    }

    p{
        font-size: .8em;
    }



    .col-md-6 {
        -ms-flex: 0 0 50%;
        flex: 0 0 49%;
        max-width: 49%;
    }

    #section-to-print p {
        font-size: .9em;
        margin-bottom: .5rem;
    }

    #section-to-print  .my-5 {
        margin-top: 1rem!important;
        margin-bottom: 1rem!important;
    }

    #section-to-print  .mt-5 {
        margin-top: 2rem!important;
    }

    #section-to-print .number{
        font-size: 1.2em;
    }

    #section-to-print .shippment-content{
        width:7.8in !important;
        padding: 10px !important;
    }


    .col-sm-4 {
        -ms-flex: 0 0 32.333333%;
        flex: 0 0 32.333333%;
        max-width: 32.333333%;
    }

    .col-sm-2 {
        -ms-flex: 0 0 15.666667%;
        flex: 0 0 15.666667%;
        max-width: 15.666667%;
    }

  }

  @media (max-width: 767.98px) {
      .card.voucher{
          width: 100% !important;
      }*/
  }


</style>
