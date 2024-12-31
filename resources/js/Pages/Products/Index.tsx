import {Head} from '@inertiajs/react';
import CeLayout from "@/Layouts/CeLayout";
import {useEffect, useState} from "react";
import PrimaryButton from "@/Components/PrimaryButton";
import UpdateStockModal from "@/Pages/Products/UpdateStockModal";
import { ToastContainer, toast } from 'react-toastify';

interface IProps {
    products: any[];
    status: string;
    message: string;
}

export default function Products({products, status, message}: Readonly<IProps>) {
    const [showUpdateStockModal, setShowUpdateStockModal] = useState(false);
    const [product, setProduct] = useState<string>('');

    useEffect(() => {
        if(status === 'success') {
            toast(message, {type: status === 'success' ? 'success' : 'error'});
        }
    }, [status, message]);

    return (
        <CeLayout>
            <Head title="Top Products"/>
            <div className="py-12">
                <ToastContainer />

                <UpdateStockModal
                    showUpdateStockModal={showUpdateStockModal}
                    setShowUpdateStockModal={setShowUpdateStockModal}
                    product={product}
                />

                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <p className="px-2 p-6 text-gray-900 text-center font-bold">
                            Top Products
                        </p>

                        <div className={'px-3'}>
                            <table
                                className="min-w-full border border-neutral-200 text-sm font-light text-surface dark:border-white/10">
                                <thead>
                                <tr className={'whitespace-nowrap px-2 py-3.5 text-center text-sm font-semibold text-gray-900'}>
                                    <th className={'py-3 text-left pl-2'}>Product</th>
                                    <th className={'py-3'}>GTIN</th>
                                    <th className={'py-3'}># Total Sold</th>
                                    <th className={'py-3'}>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                {
                                    !!products &&
                                    products.map((product, index) => (
                                        <tr key={product.merchant_product_no} className={'text-center'}>
                                            <td className={'py-3 text-left pl-2'}>{product.product_name}</td>
                                            <td className={'py-3'}>{product.gtin}</td>
                                            <td className={'py-3'}>{product.total_quantity}</td>
                                            <td className={'py-3'}>
                                                <PrimaryButton  onClick={() => {
                                                    setProduct(product)
                                                    setShowUpdateStockModal(true)
                                                }}>
                                                    Add Stock
                                                </PrimaryButton>
                                            </td>
                                        </tr>
                                    ))
                                }
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </CeLayout>
    );
}
